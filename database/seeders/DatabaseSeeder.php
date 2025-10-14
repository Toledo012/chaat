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
        DB::table('rol_permiso')->truncate();
        DB::table('permisos')->truncate();
        DB::table('cuentas')->truncate();
        DB::table('usuarios')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Insertar Roles
        DB::table('roles')->insert([
            ['id_rol' => 1, 'nombre' => 'Administrador'],
            ['id_rol' => 2, 'nombre' => 'Usuario'],
        ]);

        // 2. Insertar Permisos (opcional)
        DB::table('permisos')->insert([
            ['id_permiso' => 1, 'nombre' => 'gestion_usuarios'],
            ['id_permiso' => 2, 'nombre' => 'gestion_formatos'],
            ['id_permiso' => 3, 'nombre' => 'crear_usuarios'],
            ['id_permiso' => 4, 'nombre' => 'editar_usuarios'],
            ['id_permiso' => 5, 'nombre' => 'eliminar_usuarios'],
            ['id_permiso' => 6, 'nombre' => 'cambiar_roles'],
            ['id_permiso' => 7, 'nombre' => 'activar_cuentas'],
        ]);

        // 3. Insertar Rol-Permiso
        DB::table('rol_permiso')->insert([
            ['id_rol' => 1, 'id_permiso' => 1],
            ['id_rol' => 1, 'id_permiso' => 2],
            ['id_rol' => 1, 'id_permiso' => 3],
            ['id_rol' => 1, 'id_permiso' => 4],
            ['id_rol' => 1, 'id_permiso' => 5],
            ['id_rol' => 1, 'id_permiso' => 6],
            ['id_rol' => 1, 'id_permiso' => 7],
        ]);

        // 4. Insertar Usuarios
        DB::table('usuarios')->insert([
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
        DB::table('cuentas')->insert([
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
