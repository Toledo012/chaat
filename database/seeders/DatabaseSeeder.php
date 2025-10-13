<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tablas (opcional - solo si quieres datos frescos)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('Rol_Permiso')->truncate();
        DB::table('Permisos')->truncate();
        DB::table('Cuentas')->truncate();
        DB::table('Usuarios')->truncate();
        DB::table('Roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Insertar Roles
        DB::table('Roles')->insert([
            ['id_rol' => 1, 'nombre' => 'Administrador'],
            ['id_rol' => 2, 'nombre' => 'Usuario'],
        ]);

        // 2. Insertar Permisos (opcional)
        DB::table('Permisos')->insert([
            ['id_permiso' => 1, 'nombre' => 'gestion_usuarios'],
            ['id_permiso' => 2, 'nombre' => 'gestion_formatos'],
        ]);

        // 3. Insertar Rol-Permiso
        DB::table('Rol_Permiso')->insert([
            ['id_rol' => 1, 'id_permiso' => 1],
            ['id_rol' => 1, 'id_permiso' => 2],
        ]);

        // 4. Insertar Usuarios
        DB::table('Usuarios')->insert([
            [
                'id_usuario' => 1,
                'nombre' => 'Admin Principal',
                'departamento' => 'Sistemas',
                'puesto' => 'Administrador',
                'extension' => '100',
                'email' => 'admin@empresa.com'
            ],
            [
                'id_usuario' => 2,
                'nombre' => 'Juan Pérez',
                'departamento' => 'Ventas',
                'puesto' => 'Ejecutivo',
                'extension' => '101',
                'email' => 'juan@empresa.com'
            ],
        ]);

        // 5. Insertar Cuentas (CONTRASEÑA: "password")
        DB::table('Cuentas')->insert([
            [
                'username' => 'admin',
                'password' => Hash::make('password'),
                'estado' => 'activo',
                'id_usuario' => 1,
                'id_rol' => 1
            ],
            [
                'username' => 'usuario',
                'password' => Hash::make('password'),
                'estado' => 'activo',
                'id_usuario' => 2,
                'id_rol' => 2
            ],
        ]);

        $this->command->info('✅ Datos de prueba creados exitosamente!');
        $this->command->info('👤 Admin: admin / password');
        $this->command->info('👤 Usuario: usuario / password');
    }
}