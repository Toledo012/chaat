<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MaterialesService
{
    public function guardarMaterialesUtilizados(int $idServicio, ?array $materiales): void
    {
        if (empty($materiales)) return;

        foreach ($materiales as $mat) {
            if (!empty($mat['id_material'])) {
                DB::table('materiales_utilizados')->insert([
                    'id_servicio' => $idServicio,
                    'id_material' => $mat['id_material'],
                    'cantidad'    => $mat['cantidad'] ?? 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
