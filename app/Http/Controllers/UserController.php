<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\CatalogoMateriales;

    

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

            $materiales = CatalogoMateriales::orderBy('id_material', 'desc')
        ->take(5)
        ->get();

    return view('user.dashboard', compact('materiales'));



        return view('user.dashboard');
    }



    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required' => 'Debes ingresar una nueva contraseña.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $cuenta = Auth::user(); // Modelo Cuenta

        $cuenta->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Tu contraseña ha sido actualizada correctamente.');
    }

public function deptoDashboard()
{
    // seguridad: si no es departamento, fuera
    if (!auth()->user()->isDepartamento()) {
        return redirect()->route('user.dashboard');
    }

    return view('depto.dashboard');
}

    
}


