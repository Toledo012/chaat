<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cuenta;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function showLoginForm()
    {



        // Si ya está autenticado, redirigir según su rol
        if (Auth::check()) {
// Redirigir según rol
if (Auth::user()->isAdmin()) {
    return redirect()->route('admin.dashboard');
}

if (Auth::user()->isDepartamento()) {
    return redirect()->route('depto.dashboard');
}

return redirect()->route('user.dashboard');

        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Primero verificar si el usuario existe
        $cuenta = \App\Models\Cuenta::where('username', $request->username)->first();

        if (!$cuenta) {
            return back()->withErrors(['username' => 'Usuario no encontrado.']);
        }

        // Verificar si está activo
        if ($cuenta->estado === 'inactivo') {
            return back()->withErrors(['username' => 'Esta cuenta está desactivada. Contacta al administrador.']);
        }

        // Intentar login
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();

            // Guardar permisos en sesión para verificar cambios
            $permisos = $cuenta->permisosArray();
            session(['permisos_usuario' => $permisos]);

// Redirigir según rol
if (Auth::user()->isAdmin()) {
    return redirect()->route('admin.dashboard');
}

if (Auth::user()->isDepartamento()) {
    return redirect()->route('depto.dashboard');
}

return redirect()->route('user.dashboard');

        }

        return back()->withErrors(['username' => 'Contraseña incorrecta.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}