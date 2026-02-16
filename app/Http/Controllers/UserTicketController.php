<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserTicketController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    public function index()
    {
        $cuentaId = auth()->user()->id_cuenta;

        $disponibles = Ticket::with('creadoPor.usuario')
            ->whereNull('asignado_a')
            ->where('estado', 'nuevo')
            ->orderByDesc('id_ticket')
            ->get();

        $misTickets = Ticket::with('creadoPor.usuario')
            ->where('asignado_a', $cuentaId)
            ->orderByDesc('id_ticket')
            ->get();

        return view('user.tickets.index', compact('disponibles', 'misTickets'));
    }

    public function tomar(Ticket $ticket)
    {
        $this->tickets->tomarComoUsuario(auth()->user(), $ticket);

        return back()->with('success', 'Ticket tomado correctamente 🫡');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'solicitante'  => 'required|string|max:100',
            'descripcion'  => 'nullable|string',
            'tipo_formato' => 'required|in:a,b,c,d',
        ]);

        $cuenta = auth()->user();

        $ticket = DB::transaction(function () use ($data, $cuenta) {

            $folio = 'TCK-' . now()->format('YmdHis') . '-' . strtoupper($data['tipo_formato']);

            $ticket = Ticket::create([
                'folio'        => $folio,
                'titulo'       => $data['titulo'],
                'solicitante'  => $data['solicitante'],
                'descripcion'  => $data['descripcion'] ?? null,
                'prioridad'    => 'media',
                'tipo_formato' => $data['tipo_formato'],
                'estado'       => 'nuevo',
                'creado_por'   => $cuenta->id_cuenta,
                'asignado_a'   => null,
                'asignado_por' => null,
                'id_servicio'  => null,
            ]);

            //  Mail centralizado en el Service
            $this->tickets->notificarTicketCreado($ticket);

            return $ticket;
        });

        return back()->with('Creado', 'Ticket creado correctamente. 📥');
    }

    public function completar(Ticket $ticket)
    {
        if (in_array($ticket->estado, ['cancelado', 'completado'], true)) {
            return back()->with('error', 'Este ticket no puede completarse.');
        }

        //  Este es el flujo correcto para user (corrige el bug del id_usuario)
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
            'titulo'       => 'required|string|max:255',
            'solicitante'  => 'required|string|max:150',
            'descripcion'  => 'nullable|string|max:200',
            'tipo_formato' => 'required|in:a,b,c,d',
        ]);

        $this->tickets->actualizarComoPropietario(auth()->user(), $ticket, $data);

        return redirect()->route('user.tickets.index')->with('success', 'Ticket actualizado correctamente');
    }
}
