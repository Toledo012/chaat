<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminTicketController extends Controller
{
    // Bandeja general
public function index(Request $request)
{
    $estado = $request->get('estado');

    $query = Ticket::with(['departamento','tecnico','servicio'])
        ->orderByDesc('created_at');

    if ($estado) {
        $query->where('estado', $estado);
    }

    // ✅ Si NO es admin => técnico: solo asignados a él o creados por él
    if (!auth()->user()->isAdmin()) {
        $yo = auth()->user()->id_usuario;

        $query->where(function ($q) use ($yo) {
            $q->where('id_tecnico_asignado', $yo)
              ->orWhere('id_usuario_creador', $yo);
        });
    }

    $tickets = $query->get();

    // ✅ Lista de técnicos solo la ocupas para el admin (asignar)
    $tecnicos = [];
    if (auth()->user()->isAdmin()) {
        $tecnicos = Usuario::whereHas('cuenta', fn($q) => $q->where('id_rol', 2))->get();
    }

    return view('admin.tickets.index', compact('tickets','tecnicos','estado'));
}

    // Detalle
public function show($id_ticket)
{
    $ticket = Ticket::with(['departamento', 'tecnico', 'servicio'])
        ->findOrFail($id_ticket);

    // ✅ Si NO es admin => solo puede ver asignados a él o creados por él
    if (!auth()->user()->isAdmin()) {
        $yo = auth()->user()->id_usuario;

        $puedeVer = ((int)$ticket->id_tecnico_asignado === (int)$yo)
                 || ((int)$ticket->id_usuario_creador === (int)$yo);

        if (!$puedeVer) {
            abort(403, 'No tienes permiso para ver este ticket.');
        }
    }

    $tecnicos = [];
    if (auth()->user()->isAdmin()) {
        $tecnicos = Usuario::whereHas('cuenta', fn($q) => $q->where('id_rol', 2))->get();
    }

    return view('admin.tickets.show', compact('ticket', 'tecnicos'));
}


 public function take($id_ticket)
{
    $ticket = Ticket::findOrFail($id_ticket);

    // Si NO es admin, solo puede tomar tickets creados por él
    if (!auth()->user()->isAdmin()) {
        $yo = auth()->user()->id_usuario;

        // Solo puede “tomar” si él lo creó
        if ((int)$ticket->id_usuario_creador !== (int)$yo) {
            abort(403, 'No puedes tomar un ticket que no creaste.');
        }
    }

    // Solo si está libre
    if ($ticket->id_tecnico_asignado) {
        return back()->with('error', 'El ticket ya fue tomado.');
    }

    $ticket->update([
        'id_tecnico_asignado' => auth()->user()->id_usuario,
        'tomado_en'           => now(),
        'estado'              => 'en_proceso',
    ]);

    return back()->with('success', 'Ticket tomado correctamente.');
}


    // Admin asigna/reasigna
    public function assign(Request $request, $id_ticket)
    {
        // Seguridad: solo admin
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'id_tecnico' => 'required|exists:usuarios,id_usuario',
        ]);

        $ticket = Ticket::findOrFail($id_ticket);

        $ticket->update([
            'id_tecnico_asignado' => $request->id_tecnico,
            'asignado_por'        => Auth::user()->id_usuario,
            'asignado_en'         => now(),
            'estado'              => 'en_proceso',
        ]);

        return back()->with('success', 'Ticket asignado correctamente.');
    }

    // Admin cambia estado (pendiente / en_espera / en_proceso)
    public function setStatus(Request $request, $id_ticket)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,en_espera',
        ]);

        $ticket = Ticket::findOrFail($id_ticket);

        $ticket->update([
            'estado' => $request->estado,
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    // Cerrar ticket (requiere formato)
   public function close($id_ticket)
{
    $ticket = Ticket::findOrFail($id_ticket);

    if (!auth()->user()->isAdmin()) {
        if ((int)$ticket->id_tecnico_asignado !== (int)auth()->user()->id_usuario) {
            abort(403, 'No puedes cerrar un ticket que no está asignado a ti.');
        }
    }

    if (!$ticket->id_servicio) {
        return back()->with('error', 'No puedes cerrar el ticket sin generar el formato.');
    }

    $ticket->update([
        'estado'     => 'terminado',
        'cerrado_en' => now(),
    ]);

    return back()->with('success', 'Ticket cerrado correctamente.');
}


    public function setFormato(Request $request, $id_ticket)
{
    $ticket = Ticket::findOrFail($id_ticket);

    // ✅ Regla: técnico asignado o admin pueden elegir formato
    if (!auth()->user()->isAdmin()) {
        if ($ticket->id_tecnico_asignado !== auth()->user()->id_usuario) {
            return back()->with('error', 'Solo el técnico asignado puede seleccionar el formato.');
        }
    }

    $request->validate([
        'formato_requerido' => 'required|in:A,B,C,D',
    ]);

    $ticket->update([
        'formato_requerido' => $request->formato_requerido,
    ]);

    return back()->with('success', 'Formato asignado al ticket.');
}
public function generarFormato($id_ticket)
{
    $ticket = Ticket::findOrFail($id_ticket);

    //  técnico asignado o admin
    if (!auth()->user()->isAdmin()) {
        if ((int)$ticket->id_tecnico_asignado !== (int)auth()->user()->id_usuario) {
            return back()->with('error', 'Solo el técnico asignado puede generar el formato.');
        }
    }

    if (!$ticket->formato_requerido) {
        return back()->with('error', 'Primero selecciona el tipo de formato (A/B/C/D).');
    }

    $params = [
        'ticket_id'       => $ticket->id_ticket,
        'id_departamento' => $ticket->id_departamento,
    ];

    return match ($ticket->formato_requerido) {
        'A' => redirect()->route('admin.formatos.a', $params),
        'B' => redirect()->route('admin.formatos.b', $params),
        'C' => redirect()->route('admin.formatos.c', $params),
        'D' => redirect()->route('admin.formatos.d', $params),
        default => back()->with('error', 'Formato inválido.'),
    };
}


}
