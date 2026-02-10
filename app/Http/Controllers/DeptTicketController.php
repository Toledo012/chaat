<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreadoMail;
use App\Services\TicketService;

class DeptTicketController extends Controller
{
    /**
     * Bandeja de Departamento:
     * - Solo tickets creados por este depto (creado_por = id_cuenta actual)
     */


        protected TicketService $tickets;

    public function __construct(TicketService $tickets)
    {
        $this->tickets = $tickets;
    }
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

    /**
     * Vista create (opcional, si quieres página en vez de modal)
     */
    public function create()
    {
        return view('departamento.tickets.create');
    }

    /**
     * Crear ticket (Departamento)
     * - creado_por = cuenta depto
     * - estado = nuevo
     * - asignado_a = null (admin lo asigna o técnicos lo toman, según tu flujo)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
    'titulo'       => 'required|string|max:255',
    'solicitante'  => 'required|string|max:150',
    'descripcion'  => 'nullable|string',
]);

$folio = 'TCK-' . now()->format('YmdHis'); // ✅ ya no depende del formato

$ticket = Ticket::create([
    'folio'        => $folio,
    'titulo'       => $data['titulo'],
    'solicitante'  => $data['solicitante'],
    'descripcion'  => $data['descripcion'] ?? null,

    // ✅ defaults fijos
    'prioridad'    => 'media',
    'tipo_formato' => 'a',

    'estado'       => 'nuevo',
    'creado_por'   => auth()->user()->id_cuenta,
    'asignado_a'   => null,
    'asignado_por' => null,
    'id_servicio'  => null,
]);


        // ✅ Avisar por correo a ADMIN (y si quieres también a técnicos, aquí lo defines)
        $emails = Cuenta::with('usuario:id_usuario,email')
            ->whereIn('id_rol', [1]) // 1 = Administrador
            ->get()
            ->pluck('usuario.email')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($emails)) {
            Mail::to($emails)->send(new TicketCreadoMail($ticket));
        }

        return back()->with('success', 'Ticket enviado al Admin ✅');
    }

    /**
     * Cancelar ticket (solo si es propio y si no está completado/cancelado)
     */
    public function cancelar(Ticket $ticket)
    {
        $cuentaId = auth()->user()->id_cuenta;

        if ((int)$ticket->creado_por !== (int)$cuentaId) {
            return back()->with('error', 'No puedes cancelar un ticket que no es tuyo.');
        }

        if (in_array($ticket->estado, ['completado','cancelado'], true)) {
            return back()->with('error', 'Este ticket ya no puede cancelarse.');
        }

        $ticket->update([
            'estado' => 'cancelado',
        ]);

        return back()->with('success', 'Ticket cancelado ✅');
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
    return redirect()->route('departamento.tickets.index')->with('success', 'Ticket actualizado ✅');
}

}
