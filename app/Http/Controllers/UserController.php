<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\CatalogoMateriales;
use App\Models\Ticket; // Asegúrate de importar el modelo Ticket
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user(); // Definimos al usuario logueado

        // 1. Obtenemos los materiales recientes
        $materiales = CatalogoMateriales::orderBy('id_material', 'desc')
            ->take(5)
            ->get();

        // 2. Obtenemos los tickets asignados a este usuario
        // Nota: Asegúrate de que la columna se llame 'id_asignado' en tu tabla
        $misTickets = Ticket::where('asignado_a', $user->id_cuenta)
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Enviamos AMBAS variables en un solo return
        return view('user.dashboard', compact('materiales', 'misTickets'));
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

        $cuenta = Auth::user(); 

        $cuenta->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Tu contraseña ha sido actualizada correctamente.');
    }
}