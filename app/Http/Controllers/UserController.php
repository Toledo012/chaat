<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Ticket;
use App\Support\TicketDashboardData;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $yearNow = now()->year;
        $request->validate([
            'anio' => "nullable|integer|between:2000,{$yearNow}",
            'mes' => 'nullable|integer|between:0,12',
        ]);
        $year = (int) $request->input('anio', $yearNow);
        $month = $request->has('mes') ? (int) $request->input('mes') : ($year === $yearNow ? now()->month : 0);
        $months = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        // Solo tickets asignados a esta cuenta.
        $misTickets = Ticket::where('asignado_a', $user->id_cuenta)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $ticketStats = DB::table('tickets')->where('asignado_a', $user->id_cuenta)
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw("SUM(CASE WHEN estado NOT IN ('completado', 'cancelado') THEN 1 ELSE 0 END) AS activos")
            ->selectRaw("SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) AS completados")
            ->first();
        $ticketStats->total = (int) $ticketStats->total;
        $ticketStats->activos = (int) $ticketStats->activos;
        $ticketStats->completados = (int) $ticketStats->completados;
        $disponibles = DB::table('tickets')->whereNull('asignado_a')->where('estado', 'nuevo')->count();
        $ticketActivity = TicketDashboardData::forAccount((int) $user->id_cuenta, 'asignado_a', $year);
        $puedeVerFormatos = $user->puedeGestionarFormatos();
        $puedeVerUsuarios = collect([
            'gestion_usuarios', 'crear_usuarios', 'editar_usuarios',
            'eliminar_usuarios', 'cambiar_roles', 'activar_cuentas',
        ])->contains(fn ($permission) => $user->tienePermiso($permission));
        $formatosPorTipo = collect();
        $formatosTotal = 0;
        if ($puedeVerFormatos && $user->id_usuario) {
            $firstFormat = DB::table('servicios')->where('id_usuario', $user->id_usuario)->min('fecha');
            if ($firstFormat) {
                $ticketActivity['yearMin'] = max(2000, min($ticketActivity['yearMin'], Carbon::parse($firstFormat)->year));
            }
            $formatQuery = DB::table('servicios')->where('id_usuario', $user->id_usuario)->whereYear('fecha', $year);
            if ($month > 0) {
                $formatQuery->whereMonth('fecha', $month);
            }
            $formatosPorTipo = $formatQuery->select('tipo_formato', DB::raw('COUNT(*) AS total'))
                ->groupBy('tipo_formato')->pluck('total', 'tipo_formato');
            $formatosTotal = $formatosPorTipo->sum();
        }

        return view('user.dashboard', compact(
            'misTickets', 'ticketStats', 'disponibles', 'ticketActivity',
            'puedeVerFormatos', 'puedeVerUsuarios', 'formatosPorTipo', 'formatosTotal', 'year', 'month', 'months'
        ));
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
