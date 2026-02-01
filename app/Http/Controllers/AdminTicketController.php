<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use App\Models\Cuenta;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    public function __construct(private TicketService $tickets) {}

    public function index(Request $request)
    {
        $qEstado    = $request->get('estado');
        $qTipo      = $request->get('tipo_formato');
        $qPrioridad = $request->get('prioridad');
        $qBuscar    = $request->get('buscar');
    
    
        $query = Ticket::with(['asignadoA', 'creadoPor'])
        ->orderByDesc('id_ticket');

        // Filtros
        if ($qEstado) {
            $query->where('estado', $qEstado);
        }
        if ($qTipo) {
            $query->where('tipo_formato', $qTipo);
        }
        if ($qPrioridad) {
            $query->where('prioridad', $qPrioridad);
        }
        if ($qBuscar) {
            $query->where(function ($qq) use ($qBuscar) {
                $qq->where('folio', 'like', "%{$qBuscar}%")
                   ->orWhere('titulo', 'like', "%{$qBuscar}%");
            });
        }

        $tickets = $query->paginate(12)->withQueryString();

        // Técnicos (rol usuario = 2 en tu Cuenta)
        $tecnicos = Cuenta::where('id_rol', 2)
            ->orderBy('username')
            ->get(['id_cuenta', 'username', 'id_usuario']);

        return view('admin.tickets.index', compact(
            'tickets',
            'tecnicos',
            'qEstado',
            'qTipo',
            'qPrioridad',
            'qBuscar'
        ));
    }

    public function asignar(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'asignado_a' => ['required', 'integer', 'exists:cuentas,id_cuenta'],
        ]);

        $this->tickets->asignarComoAdmin(auth()->user(), $ticket, (int)$data['asignado_a']);

        return back()->with('Asignado', 'Ticket asignado correctamente gg');
    }

    public function cancelar(Ticket $ticket)
    {
        $this->tickets->cancelar(auth()->user(), $ticket);

        return back()->with('Cancelado', 'Ticket cancelado correctamente ');
    }


    public function store(Request $request)
{
    $data = $request->validate([
        'titulo' => 'required|string|max:255',
        'solicitante' => 'required|string|max:100',
        'descripcion' => 'nullable|string',
        'prioridad' => 'required|in:baja,media,alta',
        'tipo_formato' => 'required|in:a,b,c,d',
    ]);

    $cuenta = auth()->user();

    // Folio simple institucional para ticket (si luego lo haces consecutivo global, lo movemos a servicio)
    $folio = 'TCK-' . now()->format('YmdHis') . '-' . strtoupper($data['tipo_formato']);

    \App\Models\Ticket::create([
        'folio' => $folio,
        'titulo' => $data['titulo'],
        'solicitante' => $data ['solicitante'] ,
        'descripcion' => $data['descripcion'] ?? null,
        'prioridad' => $data['prioridad'],
        'tipo_formato' => $data['tipo_formato'],
        'estado' => 'nuevo',
        'creado_por' => $cuenta->id_cuenta,
        'asignado_a' => null,
        'asignado_por' => null,
        'id_servicio' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return back()->with('Creado', 'Ticket creado correctamente.');
}
public function completar(\App\Models\Ticket $ticket)
{
    // Evitar acciones en estados finales
    if (in_array($ticket->estado, ['cancelado', 'completado'])) {
        return back()->with('error', 'Este ticket no puede completarse.');
    }

    // Si no existe servicio, crearlo y ligarlo
    if (!$ticket->id_servicio) {
        DB::transaction(function () use ($ticket) {

            // 🔹 Generar folio institucional GLOBAL para SERVICIOS
            $tipo = strtoupper($ticket->tipo_formato); // a->A
            $folio = $this->tickets->generarFolioGlobal($tipo);

            $idServicio = DB::table('servicios')->insertGetId([
                'folio' => $folio,
                'fecha' => now()->format('Y-m-d'),
                'id_usuario' => auth()->user()->id_usuario, // quien lo está atendiendo
                'id_departamento' => null, // si luego quieres amarrarlo al depto, lo hacemos
                'tipo_formato' => $tipo,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('tickets')
                ->where('id_ticket', $ticket->id_ticket)
                ->update([
                    'id_servicio' => $idServicio,
                    'estado' => 'en_proceso',
                    'updated_at' => now(),
                ]);

            // refrescar objeto en memoria
            $ticket->id_servicio = $idServicio;
            $ticket->estado = 'en_proceso';
        });
    }

    // 🔹 Redirigir al editor del formato correspondiente
    return redirect()->route('admin.formatos.edit', [
        'tipo' => strtoupper($ticket->tipo_formato),
        'id' => $ticket->id_servicio,
    ]);
}

}
