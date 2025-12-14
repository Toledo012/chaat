<?php

namespace App\Http\Controllers;
use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::all();
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
            'nombre' => 'required|string|max:50|unique:departamentos,nombre,' . $departamento->id_departamento . ',id_departamento',
        ]);

        $departamento->update($request->only('nombre','descripcion','activo'));

        return redirect()->route('admin.departamentos.index')
            ->with('success', 'Departamento actualizado');
    }
}

