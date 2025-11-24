<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
   public function run()
    {
        // Crear usuario base
        $idUsuario = DB::table('usuarios')->insertGetId([
            'nombre'       => 'Admin Principal',
            'departamento' => 'Sistemas',
            'puesto'       => 'Administrador General',
            'extension'    => '8888',
            'email'        => 'admin@local.com',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Crear la cuenta asociada
        DB::table('cuentas')->insert([
            'username'   => 'admin@local.com',
            'password'   => Hash::make('admin123'), // ⚠️ cámbialo después
            'estado'     => 'activo',
            'id_usuario' => $idUsuario,
            'id_rol'     => 1, // Rol administrador
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Asignar permisos al administrador
        $permisos = DB::table('permisos')->pluck('id_permiso')->toArray();

        foreach ($permisos as $permiso) {
            DB::table('rol_permiso')->insertOrIgnore([
                'id_rol'     => 1,
                'id_permiso' => $permiso,
            ]);
        }

        echo "SuperAdmin creado correctamente.\n";
    }
}
