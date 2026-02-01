<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;

class DeptTicketController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    public function index()
    {
        $cuenta = auth()->user();

        $tickets = Ticket::where('creado_por', $cuenta->id_cuenta)
            ->orderByDesc('id_ticket')
            ->paginate(10);

        return view('departamento.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('departamento.tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string'],
            'prioridad'    => ['required', 'in:baja,media,alta'],
            'tipo_formato' => ['required', 'in:a,b,c,d'],
        ]);

        $this->tickets->crearComoDepartamento(auth()->user(), $data);

        return redirect()->route('departamento.tickets.index')
            ->with('success', 'Ticket creado y enviado a Sistemas.');
    }

    public function cancelar(Ticket $ticket)
    {
        $this->tickets->cancelar(auth()->user(), $ticket);

        return back()->with('success', 'Ticket cancelado.');
    }
}
