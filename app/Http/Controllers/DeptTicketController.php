<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeptTicketController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    public function index(Request $request)
    {
        $cuentaId = auth()->user()->id_cuenta;

        $qEstado = $request->get('estado');
        $qBuscar = $request->get('buscar');

        $query = Ticket::query()
            ->where('creado_por', $cuentaId)
            ->orderByDesc('id_ticket');

        if ($qEstado) {
            $query->where('estado', $qEstado);
        }

        if ($qBuscar) {
            $query->where(function ($qq) use ($qBuscar) {
                $qq->where('folio', 'like', "%{$qBuscar}%")
                    ->orWhere('titulo', 'like', "%{$qBuscar}%")
                    ->orWhere('solicitante', 'like', "%{$qBuscar}%");
            });
        }

        $tickets = $query->paginate(12)->withQueryString();

        return view('departamento.tickets.index', compact('tickets', 'qEstado', 'qBuscar'));
    }

    public function create()
    {
        return view('departamento.tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'solicitante' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'prioridad'   => 'nullable|in:baja,media,alta',
            'tipo_formato'=> 'nullable|in:a,b,c,d',
        ]);

        $ticket = DB::transaction(function () use ($data) {

            $folio = 'TCK-' . now()->format('YmdHis');

            $ticket = Ticket::create([
                'folio'        => $folio,
                'titulo'       => $data['titulo'],
                'solicitante'  => $data['solicitante'],
                'descripcion'  => $data['descripcion'] ?? null,

                'prioridad'    => $data['prioridad'] ?? 'media',
                'tipo_formato' => $data['tipo_formato'] ?? 'a',

                'estado'       => 'nuevo',
                'creado_por'   => auth()->user()->id_cuenta,
                'asignado_a'   => null,
                'asignado_por' => null,
                'id_servicio'  => null,
            ]);

            $this->tickets->notificarTicketCreado($ticket);

            return $ticket;
        });

        return back()->with('success', 'Ticket enviado al Admin');
    }

    public function cancelar(Ticket $ticket)
    {
        $this->tickets->cancelar(auth()->user(), $ticket);

        return back()->with('success', 'Ticket cancelado');
    }

    public function edit(Ticket $ticket)
    {
        return view('departamento.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'solicitante' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
        ]);

        $this->tickets->actualizarComoDepto(auth()->user(), $ticket, $data);

        return redirect()->route('departamento.tickets.index')->with('success', 'Ticket actualizado');
    }
}
