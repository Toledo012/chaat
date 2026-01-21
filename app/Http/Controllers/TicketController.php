<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    // Lista de tickets del departamento
    public function index()
    {
        $tickets = Ticket::where('id_departamento', auth()->user()->id_departamento)
            ->orderByDesc('created_at')
            ->get();

return view('departamentos.tickets.index', compact('tickets'));
    }

    // Formulario
    public function create()
    {
        return view('tickets.create');
    }

    // Guardar ticket
  public function store(Request $request)
{
    $request->validate([
        'nombre_solicitante' => 'required|string|max:100',
        'telefono'           => 'nullable|string|max:30',
        'correo_solicitante' => 'nullable|email|max:120',
        'asunto'             => 'required|string|max:150',
        'descripcion'        => 'required|string',
        'tipo_atencion'      => 'required|in:equipo,red_wifi,software_programas,otro',
        // opcional si pides detalle cuando es "otro":
        // 'tipo_atencion_otro' => 'nullable|string|max:80',
    ]);

    Ticket::create([
        'id_departamento'    => auth()->user()->id_departamento,
        'nombre_solicitante' => $request->nombre_solicitante,
        'telefono'           => $request->telefono,
        'correo_solicitante' => $request->correo_solicitante,
        'asunto'             => $request->asunto,
        'descripcion'        => $request->descripcion,
        'tipo_atencion'      => $request->tipo_atencion,

        // ✅ IMPORTANTES:
        'creado_por_tipo'    => 'departamento',
        'id_usuario_creador' => auth()->user()->id_usuario, // ✅ para auditoría y trazabilidad
        'estado'             => 'pendiente',                // ✅ estado inicial

        // opcional: timestamps si no están automáticos:
        // 'created_at' => now(),
        // 'updated_at' => now(),
    ]);

    return redirect()->route('departamentos.tickets.index')
        ->with('success', 'Ticket creado correctamente');
}


    // Ver detalle (opcional)
    public function show($id_ticket)
    {
        $ticket = Ticket::where('id_ticket', $id_ticket)
            ->where('id_departamento', auth()->user()->id_departamento)
            ->firstOrFail();

        return view('departamentos.tickets.show', compact('ticket'));
    }
}
