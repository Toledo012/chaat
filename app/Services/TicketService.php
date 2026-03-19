<?php

namespace App\Services;

use App\Mail\TicketAsignadoMail;
use App\Mail\TicketCreadoMail;
use App\Models\Cuenta;
use App\Models\Departamento;
use App\Models\Servicio;
use App\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TicketService
{
    public function __construct(
        private ServicioService $servicios
    ) {}

    /**
     * Genera sigla de departamento para folio:
     * - Si existe $departamento->clave, usa esa.
     * - Si no, toma iniciales del nombre (ignorando conectores comunes).
     */
    private function generarSiglaDepartamento(int $idDepartamento): string
    {
        $dep = Departamento::find($idDepartamento);
        if (!$dep) return 'DEP';

        // Si tu tabla tiene columna "clave" (recomendado)
        if (!empty($dep->clave)) {
            $clave = strtoupper((string) $dep->clave);
            $clave = preg_replace('/[^A-Z0-9]/', '', $clave);
            return $clave !== '' ? $clave : 'DEP';
        }

        $nombre = trim((string) ($dep->nombre ?? ''));
        if ($nombre === '') return 'DEP';

        $palabras = preg_split('/\s+/', $nombre);
        $sigla = '';

        foreach ($palabras as $p) {
            $p = trim($p);
            if ($p === '') continue;

            $lower = mb_strtolower($p);
            if (in_array($lower, ['de', 'del', 'la', 'el', 'y', 'en', 'para'], true)) continue;

            $sigla .= mb_substr($p, 0, 1);
        }

        $sigla = strtoupper($sigla);
        return $sigla !== '' ? $sigla : 'DEP';
    }

    /**
     * Folio
     * TCK-YYYYMMDD-DEPTO-01
     * consecutivo por (fecha + depto)
     *
     */
    private function generarFolio(string $tipoFormato, int $idDepartamento): string
    {
        $fecha = now()->format('Ymd');
        $sigla = $this->generarSiglaDepartamento($idDepartamento);

        $prefix = "TCK-{$fecha}-{$sigla}-";

        // Buscar el último consecutivo de ese prefijo
        $ultimo = Ticket::query()
            ->where('folio', 'like', $prefix . '%')
            ->orderByDesc('folio')
            ->value('folio');

        $next = 1;
        if ($ultimo) {
            // último "-NN" (2 dígitos). Si algún día quieres 3+ dígitos, te lo adapto.
            $num = (int) substr($ultimo, -2);
            $next = $num + 1;
        }

        return $prefix . str_pad((string) $next, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Crea ticket y reintenta si el folio choca (por concurrencia).
     */
    private function crearTicketConFolioUnico(array $payload, string $tipoFormato, int $idDepartamento): Ticket
    {
        for ($i = 0; $i < 3; $i++) {
            try {
                return Ticket::create($payload);
            } catch (QueryException $e) {
                $msg = $e->getMessage();
                $isUnique = str_contains($msg, 'Duplicate') || str_contains($msg, 'UNIQUE') || str_contains($msg, 'duplicate');

                if (!$isUnique) {
                    throw $e;
                }

                // regenerar folio y reintentar
                $payload['folio'] = $this->generarFolio($tipoFormato, $idDepartamento);
            }
        }

        throw new HttpException(500, 'No se pudo generar un folio único. Intenta nuevamente.');
    }

    // ==========================================================
    // CREACIÓN CENTRALIZADA
    // ==========================================================

    public function crearComoAdmin(Cuenta $admin, array $data): Ticket
    {
        if (!method_exists($admin, 'isAdmin') || !$admin->isAdmin()) {
            throw new HttpException(403, 'Solo Admin puede crear tickets en este flujo.');
        }

        $idDepartamento = (int) $data['id_departamento'];
        $tipoFormato = $data['tipo_formato'];

        return DB::transaction(function () use ($admin, $data, $idDepartamento, $tipoFormato) {

            $folio = $this->generarFolio($tipoFormato, $idDepartamento);

            $ticket = $this->crearTicketConFolioUnico([
                'folio'           => $folio,
                'titulo'          => $data['titulo'],
                'solicitante'     => $data['solicitante'],
                'descripcion'     => $data['descripcion'] ?? null,
                'prioridad'       => $data['prioridad'],
                'tipo_formato'    => $tipoFormato,
                'estado'          => 'nuevo',
                'creado_por'      => $admin->id_cuenta,
                'asignado_a'      => null,
                'asignado_por'    => null,
                'id_servicio'     => null,
                'id_departamento' => $idDepartamento,
            ], $tipoFormato, $idDepartamento);

            $this->notificarTicketCreado($ticket);

            return $ticket;
        });
    }

    public function crearComoUsuario(Cuenta $cuenta, array $data, bool $autoTomar = false): Ticket
    {
        if (!method_exists($cuenta, 'isUser') || !$cuenta->isUser()) {
            throw new HttpException(403, 'Solo un Usuario puede crear tickets en este flujo.');
        }

        $idDepartamento = (int) $data['id_departamento'];
        $tipoFormato = $data['tipo_formato'];

        return DB::transaction(function () use ($cuenta, $data, $autoTomar, $idDepartamento, $tipoFormato) {

            $estado = $autoTomar ? 'asignado' : 'nuevo';
            $folio  = $this->generarFolio($tipoFormato, $idDepartamento);

            $ticket = $this->crearTicketConFolioUnico([
                'folio'           => $folio,
                'titulo'          => $data['titulo'],
                'solicitante'     => $data['solicitante'],
                'descripcion'     => $data['descripcion'] ?? null,
                'prioridad'       => 'media',
                'tipo_formato'    => $tipoFormato,
                'estado'          => $estado,
                'creado_por'      => $cuenta->id_cuenta,
                'asignado_a'      => $autoTomar ? $cuenta->id_cuenta : null,
                'asignado_por'    => $autoTomar ? $cuenta->id_cuenta : null,
                'id_servicio'     => null,
                'id_departamento' => $idDepartamento,
            ], $tipoFormato, $idDepartamento);

            $this->notificarTicketCreado($ticket);

            return $ticket;
        });
    }

    public function crearComoDepartamento(Cuenta $cuenta, array $data): Ticket
    {
        if (!method_exists($cuenta, 'isDepartamento') || !$cuenta->isDepartamento()) {
            throw new HttpException(403, 'Solo un Departamento puede crear tickets en este flujo.');
        }

        //  depto amarrado al logueado, no al request
        $idDepartamento = $cuenta->usuario->id_departamento ?? $cuenta->id_departamento ?? null;
        if (!$idDepartamento) {
            throw new HttpException(422, 'Tu cuenta no tiene departamento asignado.');
        }

        $tipoFormato = $data['tipo_formato'] ?? 'a';

        return DB::transaction(function () use ($cuenta, $data, $idDepartamento, $tipoFormato) {

            $folio = $this->generarFolio($tipoFormato, (int) $idDepartamento);

            $ticket = $this->crearTicketConFolioUnico([
                'folio'           => $folio,
                'titulo'          => $data['titulo'],
                'solicitante'     => $data['solicitante'],
                'descripcion'     => $data['descripcion'] ?? null,
                'prioridad'       => $data['prioridad'] ?? 'media',
                'tipo_formato'    => $tipoFormato,
                'estado'          => 'nuevo',
                'creado_por'      => $cuenta->id_cuenta,
                'asignado_a'      => null,
                'asignado_por'    => null,
                'id_servicio'     => null,
                'id_departamento' => (int) $idDepartamento,
            ], $tipoFormato, (int) $idDepartamento);

            $this->notificarTicketCreado($ticket);

            return $ticket;
        });
    }

    // ==========================================================
    // FLUJOS EXISTENTES (ASIGNAR / TOMAR / CANCELAR / SERVICIO)
    // ==========================================================

    public function asignarComoAdmin(Cuenta $admin, Ticket $ticket, int $asignadoAIdCuenta): Ticket
    {
        if (!method_exists($admin, 'isAdmin') || !$admin->isAdmin()) {
            throw new HttpException(403, 'Solo Admin puede asignar tickets.');
        }

        if (in_array($ticket->estado, ['cancelado', 'completado'], true)) {
            throw new HttpException(422, 'No se puede asignar un ticket cancelado o completado.');
        }

        return DB::transaction(function () use ($admin, $ticket, $asignadoAIdCuenta) {

            $ticket->update([
                'asignado_a'   => $asignadoAIdCuenta,
                'asignado_por' => $admin->id_cuenta,
                'estado'       => 'asignado',
            ]);

            $ticket = $ticket->fresh(['asignadoA.usuario']);

            $this->notificarTicketAsignado($ticket);

            return $ticket;
        });
    }

    public function tomarComoUsuario(Cuenta $cuenta, Ticket $ticket): Ticket
    {
        if (!method_exists($cuenta, 'isUser') || !$cuenta->isUser()) {
            throw new HttpException(403, 'Solo Usuario puede tomar tickets.');
        }

        if (!is_null($ticket->asignado_a)) {
            throw new HttpException(422, 'Este ticket ya está asignado.');
        }

        if (in_array($ticket->estado, ['cancelado', 'completado'], true)) {
            throw new HttpException(422, 'No se puede tomar un ticket cancelado o completado.');
        }

        return DB::transaction(function () use ($cuenta, $ticket) {

            $updated = Ticket::where('id_ticket', $ticket->id_ticket)
                ->whereNull('asignado_a')
                ->whereNotIn('estado', ['cancelado', 'completado'])
                ->update([
                    'asignado_a'   => $cuenta->id_cuenta,
                    'asignado_por' => $cuenta->id_cuenta,
                    'estado'       => 'asignado',
                    'updated_at'   => now(),
                ]);

            if ($updated === 0) {
                throw new HttpException(422, 'Este ticket ya fue tomado por otro usuario.');
            }

            return Ticket::with(['asignadoA.usuario'])->findOrFail($ticket->id_ticket);
        });
    }

    public function cancelar(Cuenta $cuenta, Ticket $ticket): Ticket
    {
        if ($ticket->estado === 'completado') {
            throw new HttpException(422, 'No se puede cancelar un ticket completado.');
        }

        $esAdmin = method_exists($cuenta, 'isAdmin') && $cuenta->isAdmin();
        $esDepto = method_exists($cuenta, 'isDepartamento') && $cuenta->isDepartamento();

        if ($esAdmin) {
            // ok
        } elseif ($esDepto) {
            if ((int) $ticket->creado_por !== (int) $cuenta->id_cuenta) {
                throw new HttpException(403, 'Solo puedes cancelar tus propios tickets.');
            }
        } else {
            throw new HttpException(403, 'No tienes permiso para cancelar tickets.');
        }

        $ticket->update(['estado' => 'cancelado']);
        return $ticket->fresh();
    }

    public function queryVisiblesParaUsuario(Cuenta $cuenta)
    {
        return Ticket::query()
            ->where(function ($q) use ($cuenta) {
                $q->where('creado_por', $cuenta->id_cuenta)
                    ->orWhere('asignado_a', $cuenta->id_cuenta);
            });
    }

    /**
     * Flujo correcto: crear servicio con folio institucional vía ServicioService

     */
    public function iniciarAtencionYCrearServicioSiFalta(Cuenta $tecnicoCuenta, Ticket $ticket): Servicio
    {
        if (!method_exists($tecnicoCuenta, 'isUser') || !$tecnicoCuenta->isUser()) {
            throw new HttpException(403, 'Solo Usuario puede iniciar atención.');
        }

        if ((int) $ticket->asignado_a !== (int) $tecnicoCuenta->id_cuenta) {
            throw new HttpException(403, 'Este ticket no está asignado a ti.');
        }

        if (in_array($ticket->estado, ['cancelado', 'completado'], true)) {
            throw new HttpException(422, 'No se puede iniciar atención en un ticket cancelado o completado.');
        }

        return DB::transaction(function () use ($tecnicoCuenta, $ticket) {

            $idUsuario = (int) $tecnicoCuenta->id_usuario;
            if (!$idUsuario) {
                throw new HttpException(422, 'La cuenta del técnico no tiene id_usuario asociado.');
            }

            // Si ya existe servicio: forzar id_usuario del que atiende y poner en proceso
            if (!empty($ticket->id_servicio)) {

                Servicio::where('id_servicio', $ticket->id_servicio)
                    ->update(['id_usuario' => $idUsuario]);

                if (in_array($ticket->estado, ['asignado', 'nuevo'], true)) {
                    $ticket->update(['estado' => 'en_proceso']);
                }

                return Servicio::findOrFail($ticket->id_servicio);
            }

            // Obtener departamento del TICKET, si no usa el del user
            $idDepartamento = $ticket->id_departamento;

            if (!$idDepartamento) {
                $idDepartamento = DB::table('usuarios_formatos')
                    ->where('id_usuario', $idUsuario)
                    ->value('id_departamento');
            }
            // Crear servicio vía ServicioService (folio institucional)
            $idServicio = $this->servicios->obtenerOCrearServicio(
                null,
                $ticket->tipo_formato,
                $idDepartamento ? (int) $idDepartamento : null
            );

            $ticket->update([
                'id_servicio' => $idServicio,
                'estado'      => 'en_proceso',
            ]);

            return Servicio::findOrFail($idServicio);
        });
    }

    public function queryPoolParaUsuarios()
    {
        return Ticket::query()
            ->whereNull('asignado_a')
            ->where('estado', 'nuevo');
    }

    public function actualizarComoAdmin(Cuenta $actor, Ticket $ticket, array $data): Ticket
    {
        return DB::transaction(function () use ($ticket, $data) {
            $ticket->fill([
                'titulo'          => $data['titulo']       ?? $ticket->titulo,
                'solicitante'     => $data['solicitante']  ?? $ticket->solicitante,
                'descripcion'     => $data['descripcion']  ?? $ticket->descripcion,
                'prioridad'       => $data['prioridad']    ?? $ticket->prioridad,
                'tipo_formato'    => $data['tipo_formato'] ?? $ticket->tipo_formato,
                'estado'          => $data['estado']       ?? $ticket->estado,
                'asignado_a'      => array_key_exists('asignado_a', $data) ? $data['asignado_a'] : $ticket->asignado_a,
                'id_departamento' => $data['id_departamento'] ?? $ticket->id_departamento,
            ]);

            $ticket->save();
            return $ticket;
        });
    }

    public function actualizarComoPropietario(Cuenta $actor, Ticket $ticket, array $data): Ticket
    {
        if ((int) $ticket->creado_por !== (int) $actor->id_cuenta) {
            abort(403, 'No puedes editar un ticket que no es tuyo.');
        }

        if ($ticket->estado !== 'nuevo' || $ticket->asignado_a) {
            abort(403, 'Este ticket ya no puede editarse.');
        }

        return DB::transaction(function () use ($ticket, $data) {
            $ticket->fill([
                'titulo'      => $data['titulo'],
                'solicitante' => $data['solicitante'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);
            $ticket->save();
            return $ticket;
        });
    }
    public function actualizarComoTecnicoAsignado(Cuenta $actor, Ticket $ticket, array $data): Ticket
    {
        if (!method_exists($actor, 'isUser') || !$actor->isUser()) {
            throw new HttpException(403, 'Solo Usuario (técnico) puede editar en este flujo.');
        }

        if ((int) $ticket->asignado_a !== (int) $actor->id_cuenta) {
            throw new HttpException(403, 'Este ticket no está asignado a ti.');
        }

        if (in_array($ticket->estado, ['cancelado', 'completado'], true)) {
            throw new HttpException(403, 'No puedes editar un ticket cancelado o completado.');
        }

        return DB::transaction(function () use ($ticket, $data) {
            $ticket->forceFill([
                'titulo'          => $data['titulo'],
                'solicitante'     => $data['solicitante'],
                'descripcion'     => $data['descripcion'] ?? null,
                'tipo_formato'    => $data['tipo_formato'],
                'id_departamento' => (int) $data['id_departamento'],
            ])->save();

            return $ticket->fresh();
        });
    }

    public function actualizarComoDepto(Cuenta $actor, Ticket $ticket, array $data): Ticket
    {
        if ((int) $ticket->creado_por !== (int) $actor->id_cuenta) {
            abort(403, 'No puedes editar un ticket que no es tuyo.');
        }

        if ($ticket->estado !== 'nuevo') {
            abort(403, 'Solo puedes editar tickets en estado "nuevo".');
        }

        return DB::transaction(function () use ($ticket, $data) {
            $ticket->fill([
                'titulo'      => $data['titulo'],
                'solicitante' => $data['solicitante'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);
            $ticket->save();
            return $ticket;
        });
    }

    // ==========================================================
    // MAILS
    // ==========================================================

    public function notificarTicketCreado(Ticket $ticket): void
    {
        $emails = Cuenta::query()
            ->whereIn('id_rol', [1, 2])
            ->whereHas('usuario', fn($q) => $q->whereNotNull('email')->where('email', '!=', ''))
            ->with('usuario:id_usuario,email')
            ->get()
            ->pluck('usuario.email')
            ->unique()
            ->values()
            ->all();

        if (empty($emails)) return;

        $to = array_shift($emails);
        $m = Mail::to($to);

        if (!empty($emails)) {
            $m->bcc($emails);
        }

        $m->send(new TicketCreadoMail($ticket));
    }

    private function notificarTicketAsignado(Ticket $ticket): void
    {
        $email = $ticket->asignadoA?->usuario?->email;

        if ($email) {
            Mail::to($email)->send(new TicketAsignadoMail($ticket));
        }
    }
}
