<?php

namespace App\Http\Controllers;

use App\Models\CatalogoMateriales;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materiales = CatalogoMateriales::all();
        return view('admin.materiales.index', compact('materiales'));
    }

    public function create()
    {
        return view('admin.materiales.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:50',
        'unidad_sugerida' => 'nullable|string|max:20',
        'unidad_otro' => 'nullable|string|max:20',
    ]);

    // Si seleccionó "otro", usar el texto tecleado
    $unidad = $request->unidad_sugerida === 'otro'
        ? $request->unidad_otro
        : $request->unidad_sugerida;

    CatalogoMateriales::create([
        'nombre' => $request->nombre,
        'unidad_sugerida' => $unidad,
    ]);

    return redirect()->route('admin.materiales.index')
        ->with('success', 'Material añadido correctamente');
}

    public function edit($id)
    {
        $material = CatalogoMateriales::findOrFail($id);
        return view('admin.materiales.edit', compact('material'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([

            'nombre' => 'required|string|max:50',
            'unidad_sugerida' => 'nullable|string|max:20',
        ]);

        $material = CatalogoMateriales::findOrFail($id);
        $material->update($request->all());

        return redirect()->route('admin.materiales.index')
            ->with('success', 'Material actualizado');
    }

    public function destroy($id)
    {
        CatalogoMateriales::destroy($id);

        return redirect()->route('admin.materiales.index')
            ->with('success', 'Material eliminado');
    }

}
