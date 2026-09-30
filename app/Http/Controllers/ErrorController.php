<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Panel de fallos del sistema (solo Administrador, ver routes/web.php)
class ErrorController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado', 'pendientes');

        $errores = DB::table('errores')
            ->when($estado === 'pendientes', fn ($q) => $q->whereNull('atendido_en'))
            ->when($estado === 'atendidos', fn ($q) => $q->whereNotNull('atendido_en'))
            ->orderByDesc('ultima_vez')
            ->paginate(20)
            ->withQueryString();

        return view('admin.errores.index', compact('errores', 'estado'));
    }

    public function show($id)
    {
        $error = DB::table('errores as e')
            ->leftJoin('cuentas as c', 'c.id_cuenta', '=', 'e.id_cuenta')
            ->leftJoin('cuentas as a', 'a.id_cuenta', '=', 'e.atendido_por')
            ->select('e.*', 'c.username', 'a.username as atendido_por_username')
            ->where('e.id_error', $id)
            ->first();

        abort_unless($error, 404);

        return view('admin.errores.show', compact('error'));
    }

    // Marca como atendido, o lo reabre si ya lo estaba
    public function atender($id)
    {
        $error = DB::table('errores')->where('id_error', $id)->first();
        abort_unless($error, 404);

        $atendido = $error->atendido_en === null;

        DB::table('errores')->where('id_error', $id)->update([
            'atendido_en'  => $atendido ? now() : null,
            'atendido_por' => $atendido ? Auth::id() : null,
        ]);

        return back()->with('success', $atendido ? 'Error marcado como atendido.' : 'Error reabierto.');
    }
}
