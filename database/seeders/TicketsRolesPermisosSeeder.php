<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketsRolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * 1. ROLES
         */
        $roles = [
            'Administrador',
            'Usuario',
            'Departamento',
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(
                ['nombre' => $rol],
                ['nombre' => $rol]
            );
        }

        /**
         * Obtener IDs de roles
         */
        $rolesMap = DB::table('roles')
            ->whereIn('nombre', $roles)
            ->pluck('id_rol', 'nombre');

        /**
         * 2. PERMISOS TICKETS V2
         */
        $permisos = [
            // Departamento
            'tickets.crear',
            'tickets.ver_propios',
            'tickets.editar_propios',
            'tickets.cancelar_propios',

            // Usuario
            'tickets.tomar',
            'tickets.ver_asignados',
            'tickets.actualizar_avance',
            'tickets.completar',

            // Admin
            'tickets.ver_todos',
            'tickets.asignar',
            'tickets.reasignar',
            'tickets.cambiar_prioridad',
            'tickets.cambiar_estado_cualquiera',
            'tickets.cancelar_cualquiera',
        ];

        foreach ($permisos as $permiso) {
            DB::table('permisos')->updateOrInsert(
                ['nombre' => $permiso],
                ['nombre' => $permiso]
            );
        }

        /**
         * Obtener IDs de permisos
         */
        $permisosMap = DB::table('permisos')
            ->whereIn('nombre', $permisos)
            ->pluck('id_permiso', 'nombre');

        /**
         * 3. ASIGNACIÓN PERMISOS POR ROL
         */
        $asignaciones = [
            'Administrador' => [
                'tickets.ver_todos',
                'tickets.asignar',
                'tickets.reasignar',
                'tickets.cambiar_prioridad',
                'tickets.cambiar_estado_cualquiera',
                'tickets.cancelar_cualquiera',
            ],

            'Usuario' => [
                'tickets.crear',
                'tickets.tomar',
                'tickets.ver_asignados',
                'tickets.actualizar_avance',
                'tickets.completar',
            ],

            'Departamento' => [
                'tickets.crear',
                'tickets.ver_propios',
                'tickets.editar_propios',
                'tickets.cancelar_propios',
            ],
        ];

        foreach ($asignaciones as $rol => $listaPermisos) {
            foreach ($listaPermisos as $permiso) {
                DB::table('rol_permiso')->updateOrInsert(
                    [
                        'id_rol'     => $rolesMap[$rol],
                        'id_permiso' => $permisosMap[$permiso],
                    ],
                    []
                );
            }
        }
    }
}
