<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->puedeGestionarUsuarios()) {
            return redirect()->route('user.dashboard');
        }

        $query = DB::table('movimientos as m')
            ->leftJoin('cuentas as c', 'c.id_cuenta', '=', 'm.id_cuenta')
            ->select('m.*', 'c.username')
            ->orderByDesc('m.fecha');

        if ($request->filled('tabla')) {
            $query->where('m.tabla', $request->string('tabla'));
        }
        if ($request->filled('accion')) {
            $query->where('m.accion', strtoupper($request->string('accion')));
        }
        if ($request->filled('usuario')) {
            $query->where('c.username', 'like', '%'.$request->string('usuario').'%');
        }
        if ($request->filled('desde')) {
            $query->where('m.fecha', '>=', $request->date('desde')->format('Y-m-d 00:00:00'));
        }
        if ($request->filled('hasta')) {
            $query->where('m.fecha', '<=', $request->date('hasta')->format('Y-m-d 23:59:59'));
        }

        // Si se solicita exportación via ?export=1, devolvemos la vista imprimible
        if ($request->boolean('export')) {
            $movs = $query->limit(1000)->get();
            return view('admin.movimientos.export', [
                'movimientos' => $movs,
                'filters' => $request->only(['tabla','accion','usuario','desde','hasta']),
                'autoprint' => (bool) $request->boolean('autoprint', false),
            ]);
        }

        $movimientos = $query->paginate(25)->withQueryString();

        return view('admin.movimientos.index', compact('movimientos'));
    }

    public function export(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->puedeGestionarUsuarios()) {
            return redirect()->route('user.dashboard');
        }

        $query = DB::table('movimientos as m')
            ->leftJoin('cuentas as c', 'c.id_cuenta', '=', 'm.id_cuenta')
            ->select('m.*', 'c.username')
            ->orderByDesc('m.fecha');

        if ($request->filled('tabla')) {
            $query->where('m.tabla', $request->string('tabla'));
        }
        if ($request->filled('accion')) {
            $query->where('m.accion', strtoupper($request->string('accion')));
        }
        if ($request->filled('usuario')) {
            $query->where('c.username', 'like', '%'.$request->string('usuario').'%');
        }
        if ($request->filled('desde')) {
            $query->where('m.fecha', '>=', $request->date('desde')->format('Y-m-d 00:00:00'));
        }
        if ($request->filled('hasta')) {
            $query->where('m.fecha', '<=', $request->date('hasta')->format('Y-m-d 23:59:59'));
        }

        $movimientos = $query->limit(1000)->get();

        return view('admin.movimientos.export', [
            'movimientos' => $movimientos,
            'filters' => $request->only(['tabla','accion','usuario','desde','hasta']),
            'autoprint' => (bool) $request->boolean('autoprint', false),
        ]);
    }
}
