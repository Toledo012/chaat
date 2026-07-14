<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\Ticket;
use App\Services\TicketService;
use App\Services\ServicioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTicketController extends Controller
{
    public function __construct(
        private TicketService $tickets,
        private ServicioService $servicios
    ) {}

    public function index(Request $request)
    {
        $qEstado    = $request->get('estado');
        $qTipo      = $request->get('tipo_formato');
        $qPrioridad = $request->get('prioridad');
        $qBuscar    = $request->get('buscar');


        $query = Ticket::with([
            'asignadoA.usuario.departamentos',
            'creadoPor.usuario.departamentos',
            'departamento',
        ])
            ->orderByDesc('id_ticket');

        if ($qEstado) $query->where('estado', $qEstado);
        if ($qTipo) $query->where('tipo_formato', $qTipo);
        if ($qPrioridad) $query->where('prioridad', $qPrioridad);

        if ($qBuscar) {
            $query->where(function ($qq) use ($qBuscar) {
                $qq->where('folio', 'like', "%{$qBuscar}%")
                    ->orWhere('titulo', 'like', "%{$qBuscar}%");
            });
        }

        $departamentos = \App\Models\Departamento::orderBy('nombre')->get();

        $tickets = $query->paginate(12)->withQueryString();

        $tecnicos = Cuenta::where('id_rol', 2)
            ->orderBy('username')
            ->get(['id_cuenta', 'username', 'id_usuario']);

        return view('admin.tickets.index', compact(
            'tickets',
            'tecnicos',
            'qEstado',
            'qTipo',
            'qPrioridad',
            'departamentos',
            'qBuscar'

        ));
    }

    public function data(Request $request)
    {
        $qEstado    = $request->get('estado');
        $qTipo      = $request->get('tipo_formato');
        $qPrioridad = $request->get('prioridad');
        $qBuscar    = $request->get('buscar');

        $query = Ticket::with([
            'asignadoA.usuario.departamentos',
            'creadoPor.usuario.departamentos',
            'departamento',
        ])->orderByDesc('id_ticket');

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

        $paginated = $query->paginate(12)->withQueryString();

        $tickets = $query->get()->map(function ($ticket) {
            return [
                'id_ticket'        => $ticket->id_ticket,
                'folio'            => $ticket->folio,
                'titulo'           => $ticket->titulo,
                'solicitante'      => $ticket->solicitante,
                'descripcion'      => $ticket->descripcion,
                'prioridad'        => $ticket->prioridad,
                'tipo_formato'     => $ticket->tipo_formato,
                'estado'           => $ticket->estado,
                'id_servicio'      => $ticket->id_servicio,
                'id_departamento'  => $ticket->id_departamento,
                'area'             => $ticket->departamento?->nombre,
                'created_at'       => $ticket->created_at,
                'updated_at'       => $ticket->updated_at,
                'creado_por'       => $ticket->creadoPor ? [
                    'id_cuenta'    => $ticket->creadoPor->id_cuenta,
                    'username'     => $ticket->creadoPor->username,
                    'nombre'       => $ticket->creadoPor->nombre_mostrado,
                    'departamento' => $ticket->creadoPor->departamento_nombre,
                ] : null,
                'asignado_a'       => $ticket->asignadoA ? [
                    'id_cuenta'    => $ticket->asignadoA->id_cuenta,
                    'username'     => $ticket->asignadoA->username,
                    'nombre'       => $ticket->asignadoA->nombre_mostrado,
                    'departamento' => $ticket->asignadoA->departamento_nombre,
                ] : null,
            ];
        });

        return response()->json($tickets);
    }
    public function asignar(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'asignado_a' => ['required', 'integer', 'exists:cuentas,id_cuenta'],
        ]);

        $this->tickets->asignarComoAdmin(auth()->user(), $ticket, (int) $data['asignado_a']);

        return back()->with('Asignado', 'Ticket asignado correctamente gg');
    }

    public function cancelar(Ticket $ticket)
    {
        $this->tickets->cancelar(auth()->user(), $ticket);

        return back()->with('Cancelado', 'Ticket cancelado correctamente');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'solicitante'  => 'required|string|max:100',
            'descripcion'  => 'nullable|string',
            'prioridad'    => 'required|in:baja,media,alta',
            'tipo_formato' => 'required|in:a,b,c,d',
            'id_departamento' => 'required|integer|exists:departamentos,id_departamento',
            'asignado_a'   => 'nullable|integer|exists:cuentas,id_cuenta',
        ]);

        // Fase 0: la creación se centraliza en TicketService::crearComoAdmin,
        // que ya genera el folio estándar (TCK-fecha-SIGLA-consecutivo),
        // maneja la transacción y dispara la notificación.
        // Fase 3: si se elige técnico, el ticket nace 'asignado' y se envía
        // SOLO el correo de asignación (se omite el de "ticket nuevo").
        $ticket = $this->tickets->crearComoAdmin(
            auth()->user(),
            $data,
            $data['asignado_a'] ?? null
        );

        $msg = $ticket->asignado_a
            ? 'Ticket creado y asignado correctamente. 📥'
            : 'Ticket creado correctamente. 📥';

        return back()->with('Creado', $msg);
    }

    public function completar(Ticket $ticket)
    {
        if (in_array($ticket->estado, ['cancelado', 'completado'], true)) {
            return back()->with('error', 'Este ticket no puede completarse.');
        }

        if (!$ticket->id_servicio) {
            DB::transaction(function () use ($ticket) {

                // se jala el id_departamento relacionado a la cuenta .

                $idDepartamento = $ticket->id_departamento ?? auth()->user()->id_departamento ?? null;

                $idServicio = $this->servicios->obtenerOCrearServicio(
                    null,
                    $ticket->tipo_formato,
                    $idDepartamento
                );

                $ticket->update([
                    'id_servicio' => $idServicio,
                    'estado'      => 'en_proceso',
                    'updated_at'  => now(),

                ]);
            });

            $ticket->refresh();
        }

        $map = [
            'a' => 'admin.formatos.a',
            'b' => 'admin.formatos.b',
            'c' => 'admin.formatos.c',
            'd' => 'admin.formatos.d',
        ];

        return redirect()->route($map[$ticket->tipo_formato], [
            'id_servicio' => $ticket->id_servicio,
            'id_ticket'   => $ticket->id_ticket,
        ]);
    }

    public function edit(Ticket $ticket)
    {
        return view('admin.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'solicitante'  => 'required|string|max:150',
            'descripcion'  => 'nullable|string',
            'prioridad'    => 'required|in:baja,media,alta',
            'tipo_formato' => 'required|in:a,b,c,d',
            'estado'       => 'required|in:nuevo,asignado,en_proceso,en_espera,completado,cancelado',
            'asignado_a'   => 'nullable|integer|exists:cuentas,id_cuenta',
            'id_departamento' => 'required|integer|exists:departamentos,id_departamento',
        ]);

        $this->tickets->actualizarComoAdmin(auth()->user(), $ticket, $data);

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket actualizado');
    }
}

