<?php

namespace App\Http\Controllers;
use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {

        $departamentos = Departamento::with('usuarios')
            ->withCount('usuarios')
            ->orderBy('nombre', 'asc')
            ->get();

        return view('admin.departamentos.index', compact('departamentos'));
    }


    public function create()
    {
        return view('admin.departamentos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:departamentos,nombre',
        ]);

        Departamento::create($request->only('nombre','descripcion','activo'));

        return redirect()->route('admin.departamentos.index')
            ->with('success', 'Departamento creado correctamente');
    }


    public function edit(Departamento $departamento)
    {
        return view('admin.departamentos.edit', compact('departamento'));
    }


    //actualizar departamento
public function update(Request $request, Departamento $departamento)
{
    $request->validate([
        'nombre' => 'required|string|max:50|unique:departamentos,nombre,'
            . $departamento->id_departamento . ',id_departamento',
    ]);

    $departamento->update([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'activo' => $request->has('activo') ? 1 : 0,
    ]);

    return redirect()->route('admin.departamentos.index')
        ->with('success', 'Departamento actualizado');
}


    public function quickStore(Request $request)
    {

        $cuenta = auth()->user();

        if (!method_exists($cuenta, 'isAdmin') && !method_exists($cuenta, 'isUser')) {
            abort(403);
        }

        if (!($cuenta->isAdmin() || $cuenta->isUser())) {
            abort(403);
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:250|unique:departamentos,nombre',
            'descripcion' => 'nullable|string|max:255',
            'activo' => 'nullable|boolean',
        ]);

        $depto = Departamento::create([
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'activo' => isset($data['activo']) ? (int) $data['activo'] : 1, // default activo
        ]);

        return response()->json([
            'id_departamento' => $depto->id_departamento,
            'nombre' => $depto->nombre,
        ], 201);
    }

}

