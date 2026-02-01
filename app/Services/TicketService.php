<?php

namespace App\Services;

use App\Models\Cuenta;
use App\Models\Servicio;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TicketService
{
    public function crearComoDepartamento(Cuenta $cuenta, array $data): Ticket
    {
        if (!method_exists($cuenta, 'isDepartamento') || !$cuenta->isDepartamento()) {
            throw new HttpException(403, 'Solo un Departamento puede crear tickets en este flujo.');
        }

        return DB::transaction(function () use ($cuenta, $data) {
            return Ticket::create([
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
        });
    }

    public function crearComoUsuario(Cuenta $cuenta, array $data, bool $autoTomar = false): Ticket
    {
        if (!method_exists($cuenta, 'isUser') || !$cuenta->isUser()) {
            throw new HttpException(403, 'Solo un Usuario puede crear tickets en este flujo.');
        }

        return DB::transaction(function () use ($cuenta, $data, $autoTomar) {
            $estado = $autoTomar ? 'asignado' : 'nuevo';

            return Ticket::create([
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

            return $ticket->fresh();
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
        ->whereNotIn('estado', ['cancelado','completado'])
        ->update([
            'asignado_a'   => $cuenta->id_cuenta,
            'asignado_por' => $cuenta->id_cuenta,
            'estado'       => 'asignado',
            'updated_at'   => now(),
        ]);

    if ($updated === 0) {
        throw new HttpException(422, 'Este ticket ya fue tomado por otro usuario.');
    }

    return Ticket::findOrFail($ticket->id_ticket);
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
            if ((int)$ticket->creado_por !== (int)$cuenta->id_cuenta) {
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
     * COMPLETAR (en tu caso) = crear/obtener Servicio y mandar al formulario.
     * - Crea servicio si el ticket no tiene id_servicio
     * - Usa Cuenta->id_usuario para servicios.id_usuario (tabla usuarios)
     * - Usa usuarios.id_departamento para servicios.id_departamento
     */
    public function iniciarAtencionYCrearServicioSiFalta(Cuenta $tecnicoCuenta, Ticket $ticket): Servicio
    {
        if (!method_exists($tecnicoCuenta, 'isUser') || !$tecnicoCuenta->isUser()) {
            throw new HttpException(403, 'Solo Usuario puede iniciar atención.');
        }

        if ((int)$ticket->asignado_a !== (int)$tecnicoCuenta->id_cuenta) {
            throw new HttpException(403, 'Este ticket no está asignado a ti.');
        }

        if (in_array($ticket->estado, ['cancelado', 'completado'], true)) {
            throw new HttpException(422, 'No se puede iniciar atención en un ticket cancelado o completado.');
        }

        return DB::transaction(function () use ($tecnicoCuenta, $ticket) {
            // Si ya existe, solo asegurar estado
            if (!empty($ticket->id_servicio)) {
                if ($ticket->estado === 'asignado' || $ticket->estado === 'nuevo') {
                    $ticket->update(['estado' => 'en_proceso']);
                }
                return Servicio::findOrFail($ticket->id_servicio);
            }

            // Obtener id_departamento del técnico desde tabla usuarios
            $idUsuario = (int) $tecnicoCuenta->id_usuario;
            if (!$idUsuario) {
                throw new HttpException(422, 'La cuenta del técnico no tiene id_usuario asociado.');
            }

            $idDepartamento = DB::table('usuarios')
                ->where('id_usuario', $idUsuario)
                ->value('id_departamento');

            // Crear servicio (folio generado)
            $servicio = Servicio::create([
                'folio'          => $this->generarFolioServicio(),
                'fecha'          => now(),
                'id_usuario'     => $idUsuario,
                'id_departamento'=> $idDepartamento, // puede ser null si tu BD lo permite
                'tipo_formato'   => $ticket->tipo_formato,
            ]);

            // Ligar al ticket y poner en proceso
            $ticket->update([
                'id_servicio' => $servicio->id_servicio,
                'estado'      => 'en_proceso',
            ]);

            return $servicio;
        });
    }

    private function generarFolioServicio(): string
    {
        // Ej: SRV-20260127-AB12CD
        $rand = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        return 'SRV-' . now()->format('Ymd') . '-' . $rand;
    }

    public function queryPoolParaUsuarios()
{
    return Ticket::query()
        ->whereNull('asignado_a')
        ->where('estado', 'nuevo');
}


 public function generarFolioGlobal(string $tipoFormato): string
    {
        $lastFolio = DB::table('servicios')
            ->orderByDesc('id_servicio')
            ->lockForUpdate()
            ->value('folio');

        $lastNum = 0;
        if ($lastFolio && preg_match('/SEMAHN-[A-D]-(\d+)/', $lastFolio, $m)) {
            $lastNum = (int) $m[1];
        }

        $nextNum = $lastNum + 1;

        return 'SEMAHN-' . strtoupper($tipoFormato) . '-' . str_pad((string)$nextNum, 5, '0', STR_PAD_LEFT);
    }
}



