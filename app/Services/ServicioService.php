<?php

namespace App\Services;

use App\Mail\TicketCompletadoMail;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ServicioService
{
    public function obtenerOCrearServicio(?int $idServicio, string $tipoFormato, int $idDepartamento): int
    {
        return DB::transaction(function () use ($idServicio, $tipoFormato, $idDepartamento) {

            // Si viene un id_servicio (caso ticket o edición), usarlo
            if ($idServicio) {

                // actualizar "quién está editando/completando"
                DB::table('servicios')
                    ->where('id_servicio', $idServicio)
                    ->update([
                        'id_usuario' => Auth::user()->id_usuario, // cuenta -> usuario ligado
                        'updated_at' => now(),
                    ]);

                return (int) $idServicio;
            }

            // Caso normal (sin ticket): crear nuevo servicio con folio institucional global
            $lastFolio = DB::table('servicios')
                ->orderByDesc('id_servicio')
                ->lockForUpdate()
                ->value('folio');

            $lastNum = 0;
            if ($lastFolio && preg_match('/SEMAHN-[A-D]-(\d+)/', $lastFolio, $m)) {
                $lastNum = (int) $m[1];
            }

            $nextNum = $lastNum + 1;
            $folio = 'SEMAHN-' . strtoupper($tipoFormato) . '-' . str_pad((string) $nextNum, 5, '0', STR_PAD_LEFT);

            return (int) DB::table('servicios')->insertGetId([
                'folio'           => $folio,
                'fecha'           => now()->format('Y-m-d'),
                'id_usuario'      => Auth::user()->id_usuario,
                'id_departamento' => $idDepartamento,
                'tipo_formato'    => strtoupper($tipoFormato),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        });
    }

    public function completarTicketPorId(int $idTicket, int $idServicio): void
    {
        DB::transaction(function () use ($idTicket, $idServicio) {

            DB::table('tickets')
                ->where('id_ticket', $idTicket)
                ->update([
                    'id_servicio' => $idServicio,
                    'estado'      => 'completado',
                    'updated_at'  => now(),
                ]);

            $ticket = Ticket::with(['creadoPor.usuario'])
                ->where('id_ticket', $idTicket)
                ->firstOrFail();

            $this->notificarTicketCompletado($ticket);
        });
    }

    public function completarTicketSiExiste(int $idServicio): void
    {
        DB::transaction(function () use ($idServicio) {

            // Traer tickets ligados a este servicio (por si existiera más de uno)
            $tickets = Ticket::with(['creadoPor.usuario'])
                ->where('id_servicio', $idServicio)
                ->where('estado', '!=', 'completado')
                ->get();

            if ($tickets->isEmpty()) {
                return;
            }

            // Completar todos los que correspondan
            DB::table('tickets')
                ->where('id_servicio', $idServicio)
                ->where('estado', '!=', 'completado')
                ->update([
                    'estado'     => 'completado',
                    'updated_at' => now(),
                ]);

            // Mandar correo a cada creador
            foreach ($tickets as $ticket) {
                $this->notificarTicketCompletado($ticket);
            }
        });
    }

    private function notificarTicketCompletado(Ticket $ticket): void
    {
        $email = $ticket?->creadoPor?->usuario?->email;

        if ($email) {
            Mail::to($email)->send(new TicketCompletadoMail($ticket));
        }
    }
}
