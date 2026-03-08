<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Departamento;
use App\Services\TicketService;
use Illuminate\Http\Request;

class UserTicketController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    public function index()
    {
        $cuentaId = auth()->user()->id_cuenta;

        $departamentos = Departamento::orderBy('nombre')->get();

        $disponibles = Ticket::with('creadoPor.usuario')
            ->whereNull('asignado_a')
            ->where('estado', 'nuevo')
            ->orderByDesc('id_ticket')
            ->get();

        $misTickets = Ticket::with('creadoPor.usuario')
            ->where('asignado_a', $cuentaId)
            ->orderByDesc('id_ticket')
            ->get();

        return view('user.tickets.index', compact('disponibles', 'misTickets', 'departamentos'));
    }

    public function data()
    {
        $cuentaId = auth()->user()->id_cuenta;

        $disponibles = Ticket::with(['creadoPor:id_cuenta,username'])
            ->whereNull('asignado_a')
            ->where('estado', 'nuevo')
            ->orderByDesc('id_ticket')
            ->get()
            ->map(function ($ticket) {
                return [
                    'id_ticket'       => $ticket->id_ticket,
                    'folio'           => $ticket->folio,
                    'titulo'          => $ticket->titulo,
                    'solicitante'     => $ticket->solicitante,
                    'descripcion'     => $ticket->descripcion,
                    'prioridad'       => $ticket->prioridad,
                    'tipo_formato'    => $ticket->tipo_formato,
                    'estado'          => $ticket->estado,
                    'id_servicio'     => $ticket->id_servicio,
                    'id_departamento' => $ticket->id_departamento,
                    'created_at'      => $ticket->created_at,
                    'updated_at'      => $ticket->updated_at,
                    'creado_por'      => $ticket->creadoPor ? [
                        'id_cuenta' => $ticket->creadoPor->id_cuenta,
                        'username'  => $ticket->creadoPor->username,
                    ] : null,
                ];
            })
            ->values();

        $misTickets = Ticket::with(['creadoPor:id_cuenta,username'])
            ->where('asignado_a', $cuentaId)
            ->orderByDesc('id_ticket')
            ->get()
            ->map(function ($ticket) {
                return [
                    'id_ticket'       => $ticket->id_ticket,
                    'folio'           => $ticket->folio,
                    'titulo'          => $ticket->titulo,
                    'solicitante'     => $ticket->solicitante,
                    'descripcion'     => $ticket->descripcion,
                    'prioridad'       => $ticket->prioridad,
                    'tipo_formato'    => $ticket->tipo_formato,
                    'estado'          => $ticket->estado,
                    'id_servicio'     => $ticket->id_servicio,
                    'id_departamento' => $ticket->id_departamento,
                    'created_at'      => $ticket->created_at,
                    'updated_at'      => $ticket->updated_at,
                    'creado_por'      => $ticket->creadoPor ? [
                        'id_cuenta' => $ticket->creadoPor->id_cuenta,
                        'username'  => $ticket->creadoPor->username,
                    ] : null,
                ];
            })
            ->values();

        return response()->json([
            'disponibles' => $disponibles,
            'misTickets'  => $misTickets,
        ]);
    }
    public function tomar(Ticket $ticket)
    {
        $this->tickets->tomarComoUsuario(auth()->user(), $ticket);

        return back()->with('success', 'Ticket tomado correctamente 🫡');
    }

    /**
     * Centralizado: creación en TicketService
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'          => 'required|string|max:255',
            'solicitante'     => 'required|string|max:100',
            'descripcion'     => 'nullable|string',
            'tipo_formato'    => 'required|in:a,b,c,d',
            'id_departamento' => 'required|integer|exists:departamentos,id_departamento',
        ]);

        $this->tickets->crearComoUsuario(auth()->user(), $data, false);

        return redirect()->route('user.tickets.index')->with('Creado', 'Ticket creado correctamente. 📥');
    }

    public function completar(Ticket $ticket)
    {
        if (in_array($ticket->estado, ['cancelado', 'completado'], true)) {
            return back()->with('error', 'Este ticket no puede completarse.');
        }

        $servicio = $this->tickets->iniciarAtencionYCrearServicioSiFalta(auth()->user(), $ticket);

        $map = [
            'a' => 'admin.formatos.a',
            'b' => 'admin.formatos.b',
            'c' => 'admin.formatos.c',
            'd' => 'admin.formatos.d',
        ];

        return redirect()->route($map[$ticket->tipo_formato], [
            'id_servicio' => $servicio->id_servicio,
            'id_ticket'   => $ticket->id_ticket,
        ]);
    }

    public function edit(Ticket $ticket)
    {
        return view('user.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'titulo'          => 'required|string|max:255',
            'solicitante'     => 'required|string|max:150',
            'descripcion'     => 'nullable|string|max:200',
            'tipo_formato'    => 'required|in:a,b,c,d',
            'id_departamento' => 'required|integer|exists:departamentos,id_departamento',
        ]);

        $this->tickets->actualizarComoTecnicoAsignado(auth()->user(), $ticket, $data);
        return redirect()->route('user.tickets.index')->with('success', 'Ticket actualizado correctamente');
    }
}
