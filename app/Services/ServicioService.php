<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
}


    public function completarTicketSiExiste(int $idServicio): void
    {
        DB::table('tickets')
            ->where('id_servicio', $idServicio)
            ->update([
                'estado' => 'completado',
                'updated_at' => now(),
            ]);
    }
}
