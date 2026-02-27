<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Departamento;
use App\Services\TicketService;
use Illuminate\Http\Request;

class DeptTicketController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    public function index(Request $request)
    {
        $cuenta = auth()->user();
        $cuentaId = $cuenta->id_cuenta;

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

        // ✅ mini cambio: para mostrar el nombre del depto en el modal de crear
        $deptId = $cuenta->usuario->id_departamento ?? $cuenta->id_departamento ?? null;
        $departamento = $deptId ? Departamento::find($deptId) : null;

        return view('departamento.tickets.index', compact('tickets', 'qEstado', 'qBuscar', 'departamento'));
    }

    public function create()
    {
        return view('departamento.tickets.create');
    }

    /**
     * ✅ Centralizado: creación en TicketService
     * (id_departamento sale del logueado dentro del service)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'solicitante'  => 'required|string|max:150',
            'descripcion'  => 'nullable|string',
            'prioridad'    => 'nullable|in:baja,media,alta',
            'tipo_formato' => 'nullable|in:a,b,c,d',
        ]);

        $this->tickets->crearComoDepartamento(auth()->user(), $data);

        return redirect()->route('departamento.tickets.index')->with('success', 'Ticket enviado al Admin');
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
