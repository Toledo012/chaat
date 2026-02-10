<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Cuenta extends Authenticatable
{
    protected $table = 'cuentas';
    protected $primaryKey = 'id_cuenta';
    
    protected $fillable = [
        'username',
        'password',
        'estado',
        'id_usuario',
        'id_rol'
    ];

    protected $hidden = ['password'];

    // Deshabilitar remember_token
    public function getRememberToken() { return null; }
    public function setRememberToken($value) {}
    public function getRememberTokenName() { return null; }

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function isAdmin()
    {
        return (int) $this->id_rol === 1;
    }

    public function isUser()
    {
        return (int) $this->id_rol === 2;
    }

    public function isDepartamento()
    {
        return (int) $this->id_rol === 3;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Verificar si tiene un permiso específico
     */
    public function tienePermiso($permiso)
    {
        // Los administradores tienen todos los permisos
        if ($this->isAdmin()) {
            return true;
        }

        // Verificar permiso en la tabla rol_permiso
        $hasByName = DB::table('rol_permiso')
            ->join('permisos', 'rol_permiso.id_permiso', '=', 'permisos.id_permiso')
            ->where('rol_permiso.id_rol', $this->id_rol)
            ->where('permisos.nombre', $permiso)
            ->exists();

        if ($hasByName) {
            return true;
        }

        // Fallback: si por alguna razón falta el registro en 'permisos',
        // intentamos validar por ID conocido (congruente con la UI y seeder)
        $map = [
            'gestion_usuarios' => 1,
            'gestion_formatos' => 2,
            'crear_usuarios' => 3,
            'editar_usuarios' => 4,
            'eliminar_usuarios' => 5,
            'cambiar_roles' => 6,
            'activar_cuentas' => 7,
        ];

        if (isset($map[$permiso])) {
            return DB::table('rol_permiso')
                ->where('id_rol', $this->id_rol)
                ->where('id_permiso', $map[$permiso])
                ->exists();
        }

        return false;
    }

    /**
     * Métodos específicos de permisos
     */
    public function puedeGestionarUsuarios()
    {
        return $this->tienePermiso('gestion_usuarios') || in_array(1, $this->permisosArray(), true);
    }

    public function puedeCrearUsuarios()
    {
        return $this->tienePermiso('crear_usuarios') || in_array(3, $this->permisosArray(), true);
    }

    public function puedeEditarUsuarios()
    {
        return $this->tienePermiso('editar_usuarios') || in_array(4, $this->permisosArray(), true);
    }

    public function puedeEliminarUsuarios()
    {
        return $this->tienePermiso('eliminar_usuarios') || in_array(5, $this->permisosArray(), true);
    }

    public function puedeCambiarRoles()
    {
        return $this->tienePermiso('cambiar_roles') || in_array(6, $this->permisosArray(), true);
    }

    public function puedeActivarCuentas()
    {
        return $this->tienePermiso('activar_cuentas') || in_array(7, $this->permisosArray(), true);
    }

    public function puedeGestionarFormatos()
    {
        return $this->tienePermiso('gestion_formatos') || in_array(2, $this->permisosArray(), true);
    }

    /**
     * ✅ NUEVO: Obtener array de IDs de permisos del usuario
     */
    public function permisosArray()
    {
        return DB::table('rol_permiso')
            ->where('id_rol', $this->id_rol)
            ->pluck('id_permiso')
            ->map(function($v){ return (int) $v; })
            ->toArray();
    }

    /**
     * ✅ NUEVO: Obtener nombres de permisos del usuario
     */
    public function permisosNombres()
    {
        $nombres = DB::table('rol_permiso')
            ->join('permisos', 'rol_permiso.id_permiso', '=', 'permisos.id_permiso')
            ->where('rol_permiso.id_rol', $this->id_rol)
            ->pluck('permisos.nombre')
            ->toArray();

        // Mapear nombres amigables
        $nombresAmigables = [
            'gestion_usuarios' => 'Ver Usuarios',
            'gestion_formatos' => 'Gestionar Formatos',
            'crear_usuarios' => 'Crear Usuarios',
            'editar_usuarios' => 'Editar Usuarios',
            'eliminar_usuarios' => 'Eliminar Usuarios',
            'cambiar_roles' => 'Cambiar Roles',
            'activar_cuentas' => 'Activar Cuentas'
        ];

        return array_map(function($nombre) use ($nombresAmigables) {
            return $nombresAmigables[$nombre] ?? $nombre;
        }, $nombres);
    }

    /**
     * Actualizar permisos del rol
     */
    public function actualizarPermisos($nuevosPermisos)
    {
        // Eliminar permisos actuales del rol
        DB::table('rol_permiso')->where('id_rol', $this->id_rol)->delete();
        
        // Insertar nuevos permisos
        foreach ($nuevosPermisos as $permisoId) {
            DB::table('rol_permiso')->insert([
                'id_rol' => $this->id_rol,
                'id_permiso' => $permisoId
            ]);
        }
        
        return true;
    }
}
