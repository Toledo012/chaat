<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCompletadoMail;
use App\Models\Ticket;


class ServicioService
{
    public function obtenerOCrearServicio(?int $idServicio, string $tipoFormato, int $idDepartamento): int
    {
        // Si viene un id_servicio (caso ticket), usarlo
        if ($idServicio) {
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
        $folio = 'SEMAHN-' . strtoupper($tipoFormato) . '-' . str_pad((string)$nextNum, 5, '0', STR_PAD_LEFT);

        return (int) DB::table('servicios')->insertGetId([
            'folio' => $folio,
            'fecha' => now()->format('Y-m-d'),
            'id_usuario' => Auth::user()->id_usuario,
            'id_departamento' => $idDepartamento,
            'tipo_formato' => strtoupper($tipoFormato),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
public function completarTicketPorId(int $idTicket, int $idServicio): void
{
    DB::table('tickets')
        ->where('id_ticket', $idTicket)
        ->update([
            'id_servicio' => $idServicio,
            'estado' => 'completado',
            'updated_at' => now(),
        ]);

    $ticket = Ticket::with(['creadoPor.usuario'])
        ->where('id_ticket', $idTicket)
        ->first();

    $email = $ticket?->creadoPor?->usuario?->email;

    if ($email) {
        Mail::to($email)->send(new TicketCompletadoMail($ticket));
    }
}



public function completarTicketSiExiste(int $idServicio): void
{
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
            'estado' => 'completado',
            'updated_at' => now(),
        ]);

    // Mandar correo a cada creador
    foreach ($tickets as $ticket) {
        $email = $ticket?->creadoPor?->usuario?->email;

        if ($email) {
            Mail::to($email)->send(new TicketCompletadoMail($ticket));
        }
    }
}
}
