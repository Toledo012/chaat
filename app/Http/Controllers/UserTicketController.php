<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;

class UserTicketController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    /**
     * Usuario ve:
     * - tickets creados por él (los tome o no)
     * - tickets asignados por admin (asignado_a = él)
     */
   public function index()
{
    $cuenta = auth()->user();

    $misAsignados = Ticket::where('asignado_a', $cuenta->id_cuenta)
        ->orderByDesc('id_ticket')
        ->paginate(10, ['*'], 'asignados');

    $disponibles = $this->tickets->queryPoolParaUsuarios()
        ->orderByDesc('id_ticket')
        ->paginate(10, ['*'], 'pool');

    $misCreados = Ticket::where('creado_por', $cuenta->id_cuenta)
        ->orderByDesc('id_ticket')
        ->paginate(10, ['*'], 'mios');

    return view('user.tickets.index', compact('misAsignados', 'disponibles', 'misCreados'));
}


    public function create()
    {
        return view('user.tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string'],
            'prioridad'    => ['required', 'in:baja,media,alta'],
            'tipo_formato' => ['required', 'in:a,b,c,d'],
            'auto_tomar'   => ['nullable', 'boolean'],
        ]);

        $autoTomar = (bool)($data['auto_tomar'] ?? false);

        $this->tickets->crearComoUsuario(auth()->user(), $data, $autoTomar);

        return redirect()->route('user.tickets.index')
            ->with('success', $autoTomar ? 'Ticket creado y tomado.' : 'Ticket creado y enviado a Admin.');
    }

    public function tomar(Ticket $ticket)
    {
        $this->tickets->tomarComoUsuario(auth()->user(), $ticket);
        return back()->with('success', 'Ticket tomado correctamente.');
    }

    /**
     * COMPLETAR = arrancar atención y redirigir al formulario.
     * (Aquí NO marcamos completado; eso pasa cuando el formato se guarda.)
     */
    public function completar(Ticket $ticket)
    {
        $cuenta = auth()->user();

        // Solo el asignado puede completar
        if ((int)$ticket->asignado_a !== (int)$cuenta->id_cuenta) {
            abort(403);
        }

        $servicio = $this->tickets->iniciarAtencionYCrearServicioSiFalta($cuenta, $ticket);

        // Reusar tu ruta existente: /admin/formatos/editar/{tipo}/{id}
        return redirect()->route('admin.formatos.edit', [
            'tipo' => $ticket->tipo_formato,
            'id'   => $servicio->id_servicio,
        ]);
    }
}
