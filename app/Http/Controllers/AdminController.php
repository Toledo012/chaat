<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Cuenta;
use App\Models\Servicio;
use App\Models\Ticket;
use App\Models\Departamento;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isAdmin()) {
            return redirect()->route('user.dashboard');
        }

        $stats = [
            'total_usuarios' => DB::table('usuarios')->count(),
            'total_servicios' => DB::table('servicios')->count(),
            'total_formatos' => 0,
            'cuentas_activas' => DB::table('cuentas')->where('estado', 'activo')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function usersIndex(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        $puedeVerGestion = $user->isAdmin() ||
            $user->puedeGestionarUsuarios() ||
            $user->puedeCrearUsuarios() ||
            $user->puedeEditarUsuarios() ||
            $user->puedeEliminarUsuarios() ||
            $user->puedeCambiarRoles() ||
            $user->puedeActivarCuentas() ||
            $user->puedeVertickets() ||
            $user->puedeCrearTickets() ||
            $user->puedeTomarTickets()||
            $user->puedeCerrarTickets();
            
        if (!$puedeVerGestion) {
            return redirect()->route('user.dashboard');
        }
        $usuarios = Usuario::with(['cuenta', 'departamentos'])->get();
     $departamentos = Departamento::orderBy('nombre')->get();

        return view('admin.users.index', compact('usuarios', 'departamentos'));
    }
public function updateUserRole(Request $request, $id)
{
    if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeCambiarRoles())) {
        return redirect()->route('user.dashboard');
    }

    $usuario = Usuario::find($id);
    
    if ($usuario && $usuario->cuenta) {

        // Nadie puede cambiar el rol del Super Admin (id_usuario = 1)
        if ($usuario->id_usuario == 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'El Super Admin no puede ser modificado.');
        }

        // Solo el Super Admin puede cambiar roles de Administradores
        if ($usuario->cuenta->id_rol == 1 && auth()->user()->id_usuario != 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Solo el Super Admin puede cambiar el rol de un Administrador.');
        }

        // Un usuario NO puede cambiarse a sí mismo el rol
        if ($usuario->id_usuario == auth()->user()->id_usuario) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes cambiar tu propio rol.');
        }

        // Solo el Super Admin puede ascender usuarios a Administradores
        if ($request->rol == 1 && auth()->user()->id_usuario != 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Solo el Super Admin puede asignar el rol de Administrador.');
        }

        // permitir cambio de rol
        $usuario->cuenta->update([
            'id_rol' => $request->rol
        ]);
    }

    return redirect()->route('admin.users.index')->with('success', 'Rol actualizado correctamente');
}

public function updateUserPermissions(Request $request, $id)
{
    if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeCambiarRoles())) {
        return redirect()->route('user.dashboard');
    }

    $usuario = Usuario::find($id);

    if ($usuario && $usuario->cuenta) {

        // solo Super Admin puede editar permisos de un Administrador
        if ($usuario->cuenta->id_rol == 1 && auth()->user()->id_usuario != 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Solo el Super Admin puede editar a un Administrador.');
        }

                if ($usuario->id_usuario == auth()->user()->id_usuario) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes cambiar tu propio permiso.');
        }

        $permisos = $request->input('permisos', []);

        // Actualizar permisos del rol
        $usuario->cuenta->actualizarPermisos($permisos);

        // Si el usuario modificado es el mismo que está logueado, actualizar su sesión
        if (auth()->user()->id_cuenta == $usuario->cuenta->id_cuenta) {
            $nuevosPermisos = $usuario->cuenta->permisosArray();
            session(['permisos_usuario' => $nuevosPermisos]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Permisos actualizados correctamente');
    }

    return redirect()->route('admin.users.index')->with('error', 'Error al actualizar permisos');
}
public function toggleUserStatus(Request $request, $id)
{
    if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeActivarCuentas())) {
        return redirect()->route('user.dashboard');
    }

    $usuario = Usuario::find($id);
    
    if ($usuario && $usuario->cuenta) {


            
                if ($usuario->id_usuario == auth()->user()->id_usuario) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes desactivar al Super Admin.');
        }


        // solo Super Admin puede activar/desactivar un Administrador
        if ($usuario->cuenta->id_rol == 1 && auth()->user()->id_usuario != 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Solo el Super Admin puede activar o desactivar Administradores.');
        }

        $nuevoEstado = $usuario->cuenta->estado == 'activo' ? 'inactivo' : 'activo';

        $usuario->cuenta->update([
            'estado' => $nuevoEstado
        ]);
    }

    return redirect()->route('admin.users.index')->with('success', 'Estado de cuenta actualizado');
}

    public function updateUser(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeEditarUsuarios())) {
            return redirect()->route('user.dashboard');
        }

        $usuario = Usuario::find($id);



        // solo el Super Admin puede editar a un Administrador
        if ($usuario->cuenta && $usuario->cuenta->id_rol == 1 && auth()->user()->id_usuario != 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Solo el Super Admin puede editar Administradores.');
        }
        if ($usuario) {
            $usernameRule = 'nullable|string|max:30|unique:cuentas,username';
            if ($usuario->cuenta) {
                $usernameRule = 'nullable|string|max:30|unique:cuentas,username,' . $usuario->cuenta->id_cuenta . ',id_cuenta';
            }

            $request->validate([
                'nombre' => 'required|string|max:30',
                'id_departamento' => 'required|exists:departamentos,id_departamento',
                'puesto' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:50|unique:usuarios,email,' . $id . ',id_usuario',
                'username' => $usernameRule,
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            $usuario->update([
                'nombre' => $request->nombre,
                'id_departamento' => $request->id_departamento,
                'puesto' => $request->puesto,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $usuario->password,
            ]);

            if ($usuario->cuenta && $request->username) {
                $usuario->cuenta->update([
                    'username' => $request->username
                    
                ]);

            if ($request->password) {
                    $usuario->cuenta->update([
                        'password' => Hash::make($request->password)
                    ]);
                }
            }

            return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente');
        }

        return redirect()->route('admin.users.index')->with('error', 'Usuario no encontrado');
    }

    public function createUserAccount(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeCrearUsuarios())) {
            return redirect()->route('user.dashboard');
        }

        $usuario = Usuario::find($id);
        
        if ($usuario) {
            $tempPassword = Str::random(16);
            Cuenta::create([
                'username' => strtolower(str_replace(' ', '.', $usuario->nombre)),
                'password' => Hash::make($tempPassword),
                'estado' => 'activo',
                'id_usuario' => $usuario->id_usuario,
                'id_rol' => 2
            ]);



            return redirect()->route('admin.users.index')->with('success', 'Cuenta creada. Password temporal: ' . $tempPassword);
        }

        return redirect()->route('admin.users.index')->with('error', 'No se pudo crear la cuenta');
    }

    public function storeUser(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeCrearUsuarios())) {
            return redirect()->route('user.dashboard');
        }

        $request->validate([
            'nombre' => 'required|string|max:30',
            'username' => 'required|string|unique:cuentas,username',
            'password' => 'required|string|min:6',
            'id_departamento' => 'required|exists:departamentos,id_departamento',

            'rol' => 'required|in:1,2,4', // 1=Admin, 2=Técnico, 4=Departamento
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'id_departamento' => $request->id_departamento, //se relaciona con departamentos
            'puesto' => $request->puesto,
            'extension' => $request->extension,
            'email' => $request->email
        ]);

        Cuenta::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'estado' => 'activo',
            'id_usuario' => $usuario->id_usuario,
            'id_rol' => $request->rol
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario y cuenta creados exitosamente');
    }

public function destroyUser(Request $request, $id)
{
    if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeEliminarUsuarios())) {
        return redirect()->route('user.dashboard');
    }

    $usuario = Usuario::find($id);
    
    if ($usuario) {

        // condicion para no poder eliminarse a si mismo
        if (auth()->user()->id_usuario == $usuario->id_usuario) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar tu propia cuenta');
        }

        // solo el admin puede eliminar a otro admin
        if ($usuario->cuenta && $usuario->cuenta->id_rol == 1) {
            
            // si el que intenta eliminar NO es el super admin
            if (auth()->user()->id_usuario != 1) { 
                return redirect()->route('admin.users.index')
                    ->with('error', 'Solo el Super Admin puede eliminar a un Administrador.');
            }
        }

        //  Si tiene cuenta, eliminar cuenta primero
        if ($usuario->cuenta) {
            $usuario->cuenta->delete();
        }
        
        //  Eliminar usuario
        $usuario->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado exitosamente');
    }

    return redirect()->route('admin.users.index')->with('error', 'Usuario no encontrado');
}


    public function redirectUserPermissions(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeCambiarRoles())) {
            return redirect()->route('user.dashboard');
        }

        return redirect()->route('admin.users.index')->with('open_permissions_id', (int) $id);
    }



    
}
