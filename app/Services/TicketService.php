<?php

namespace App\Services;

use App\Mail\TicketAsignadoMail;
use App\Mail\TicketCreadoMail;
use App\Models\Cuenta;
use App\Models\Servicio;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TicketService
{
    public function crearComoDepartamento(Cuenta $cuenta, array $data): Ticket
    {
        if (!method_exists($cuenta, 'isDepartamento') || !$cuenta->isDepartamento()) {
            throw new HttpException(403, 'Solo un Departamento puede crear tickets en este flujo.');
        }

        return DB::transaction(function () use ($cuenta, $data) {

            $ticket = Ticket::create([
                'titulo'       => $data['titulo'],
                'descripcion'  => $data['descripcion'] ?? null,
                'prioridad'    => $data['prioridad'] ?? 'media',
                'tipo_formato' => $data['tipo_formato'], // a|b|c|d
                'estado'       => 'nuevo',
                'creado_por'   => $cuenta->id_cuenta,
                'asignado_a'   => null,
                'asignado_por' => null,
                'id_servicio'  => null,
            ]);

            $this->notificarTicketCreado($ticket);

            return $ticket;
        });
    }

    public function crearComoUsuario(Cuenta $cuenta, array $data, bool $autoTomar = false): Ticket
    {
        if (!method_exists($cuenta, 'isUser') || !$cuenta->isUser()) {
            throw new HttpException(403, 'Solo un Usuario puede crear tickets en este flujo.');
        }

        return DB::transaction(function () use ($cuenta, $data, $autoTomar) {
            $estado = $autoTomar ? 'asignado' : 'nuevo';

            $ticket = Ticket::create([
                'titulo'       => $data['titulo'],
                'descripcion'  => $data['descripcion'] ?? null,
                'prioridad'    => $data['prioridad'] ?? 'media',
                'tipo_formato' => $data['tipo_formato'], // a|b|c|d
                'estado'       => $estado,
                'creado_por'   => $cuenta->id_cuenta,
                'asignado_a'   => $autoTomar ? $cuenta->id_cuenta : null,
                'asignado_por' => $autoTomar ? $cuenta->id_cuenta : null,
                'id_servicio'  => null,
            ]);

            $this->notificarTicketCreado($ticket);

            // Si auto-tomar cuenta como "asignación", opcional:
            // $this->notificarTicketAsignado($ticket->fresh(['asignadoA.usuario']));

            return $ticket;
        });
    }

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

            // refrescar y cargar relación necesaria para el email
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

            // opcional: notificar al que tomó (o admins). Si quieres, lo activamos.
            $ticketActualizado = Ticket::with(['asignadoA.usuario'])->findOrFail($ticket->id_ticket);
            // $this->notificarTicketAsignado($ticketActualizado);

            return $ticketActualizado;
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

            // Si ya existe servicio, forzar id_usuario del que está atendiendo
            if (!empty($ticket->id_servicio)) {

                Servicio::where('id_servicio', $ticket->id_servicio)
                    ->update(['id_usuario' => $idUsuario]);

                if (in_array($ticket->estado, ['asignado','nuevo'], true)) {
                    $ticket->update(['estado' => 'en_proceso']);
                }

                return Servicio::findOrFail($ticket->id_servicio);
            }

            $idDepartamento = DB::table('usuarios')
                ->where('id_usuario', $idUsuario)
                ->value('id_departamento');

            $servicio = Servicio::create([
                'folio'           => $this->generarFolioServicio(),
                'fecha'           => now(),
                'id_usuario'      => $idUsuario,
                'id_departamento' => $idDepartamento,
                'tipo_formato'    => $ticket->tipo_formato,
            ]);

            $ticket->update([
                'id_servicio' => $servicio->id_servicio,
                'estado'      => 'en_proceso',
            ]);

            return $servicio;
        });
    }

    private function generarFolioServicio(): string
    {
        $rand = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        return 'SRV-' . now()->format('Ymd') . '-' . $rand;
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
                'titulo'       => $data['titulo']       ?? $ticket->titulo,
                'solicitante'  => $data['solicitante']  ?? $ticket->solicitante,
                'descripcion'  => $data['descripcion']  ?? $ticket->descripcion,
                'prioridad'    => $data['prioridad']    ?? $ticket->prioridad,
                'tipo_formato' => $data['tipo_formato'] ?? $ticket->tipo_formato,
                'estado'       => $data['estado']       ?? $ticket->estado,
                'asignado_a'   => array_key_exists('asignado_a', $data) ? $data['asignado_a'] : $ticket->asignado_a,
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

    public function notificarTicketCreado(Ticket $ticket): void
    {
        $emails = Cuenta::query()
            ->whereIn('id_rol', [1,2])
            ->whereHas('usuario', fn($q) => $q->whereNotNull('email')->where('email','!=',''))
            ->with('usuario:id_usuario,email')
            ->get()
            ->pluck('usuario.email')
            ->unique()
            ->values()
            ->all();

        if (empty($emails)) return;

        $to = array_shift($emails); // primer correo
        $m = Mail::to($to);

        if (!empty($emails)) {
            $m->bcc($emails);
        }

        $m->send(new \App\Mail\TicketCreadoMail($ticket));
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


    private function notificarTicketAsignado(Ticket $ticket): void
    {
        $email = $ticket->asignadoA?->usuario?->email;

        if ($email) {
            Mail::to($email)->send(new TicketAsignadoMail($ticket));
        }
    }
}
