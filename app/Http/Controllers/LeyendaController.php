<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Leyenda anual del encabezado de los formatos (solo Administrador, ver routes/web.php)
class LeyendaController extends Controller
{
    public function index()
    {
        $leyendas = DB::table('leyendas')->orderByDesc('anio')->get();

        return view('admin.leyendas.index', compact('leyendas'));
    }

    // Crea la leyenda del año o la reemplaza si ese año ya existe
    public function guardar(Request $request)
    {
        $data = $request->validate([
            'anio'  => 'required|integer|min:2000|max:2100',
            'texto' => 'required|string|max:255',
        ]);

        $existe = DB::table('leyendas')->where('anio', $data['anio'])->exists();

        DB::table('leyendas')->updateOrInsert(
            ['anio' => $data['anio']],
            ['texto' => trim($data['texto']), 'updated_at' => now()] + ($existe ? [] : ['created_at' => now()])
        );

        return redirect()->route('admin.leyendas.index')
            ->with('success', "Leyenda {$data['anio']} " . ($existe ? 'actualizada.' : 'agregada.'));
    }
}
