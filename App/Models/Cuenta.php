<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Cuenta extends Authenticatable
{
    protected $table = 'Cuentas';
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
        return $this->id_rol === 1;
    }

    public function isUser()
    {
        return $this->id_rol === 2;
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

        // Verificar permiso en la tabla Rol_Permiso
        return DB::table('Rol_Permiso')
            ->join('Permisos', 'Rol_Permiso.id_permiso', '=', 'Permisos.id_permiso')
            ->where('Rol_Permiso.id_rol', $this->id_rol)
            ->where('Permisos.nombre', $permiso)
            ->exists();
    }

    /**
     * Métodos específicos de permisos
     */
    public function puedeGestionarUsuarios()
    {
        return $this->tienePermiso('gestion_usuarios');
    }

    public function puedeCrearUsuarios()
    {
        return $this->tienePermiso('crear_usuarios');
    }

    public function puedeEditarUsuarios()
    {
        return $this->tienePermiso('editar_usuarios');
    }

    public function puedeEliminarUsuarios()
    {
        return $this->tienePermiso('eliminar_usuarios');
    }

    public function puedeCambiarRoles()
    {
        return $this->tienePermiso('cambiar_roles');
    }

    public function puedeActivarCuentas()
    {
        return $this->tienePermiso('activar_cuentas');
    }

    public function puedeGestionarFormatos()
    {
        return $this->tienePermiso('gestion_formatos');
    }

    /**
     * ✅ NUEVO: Obtener array de IDs de permisos del usuario
     */
    public function permisosArray()
    {
        return DB::table('Rol_Permiso')
            ->where('id_rol', $this->id_rol)
            ->pluck('id_permiso')
            ->toArray();
    }

    /**
     * ✅ NUEVO: Obtener nombres de permisos del usuario
     */
    public function permisosNombres()
    {
        $nombres = DB::table('Rol_Permiso')
            ->join('Permisos', 'Rol_Permiso.id_permiso', '=', 'Permisos.id_permiso')
            ->where('Rol_Permiso.id_rol', $this->id_rol)
            ->pluck('Permisos.nombre')
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
     * ✅ NUEVO: Actualizar permisos del rol
     */
    public function actualizarPermisos($nuevosPermisos)
    {
        // Eliminar permisos actuales del rol
        DB::table('Rol_Permiso')->where('id_rol', $this->id_rol)->delete();
        
        // Insertar nuevos permisos
        foreach ($nuevosPermisos as $permisoId) {
            DB::table('Rol_Permiso')->insert([
                'id_rol' => $this->id_rol,
                'id_permiso' => $permisoId
            ]);
        }
        
        return true;
    }
}