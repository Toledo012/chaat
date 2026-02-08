<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TicketService;
use App\Models\Cuenta;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreadoMail;


class UserTicketController extends Controller
{
    public function __construct(
        private TicketService $tickets
    ) {}

    /**
     * Bandeja del técnico
     * - Tickets disponibles (sin asignar)
     * - Mis tickets
     */
    public function index()
    {
        $cuentaId = auth()->user()->id_cuenta;

        // Tickets disponibles (nadie los ha tomado)
        $disponibles = Ticket::with('creadoPor.usuario')
            ->whereNull('asignado_a')
            ->where('estado', 'nuevo')
            ->orderByDesc('id_ticket')
            ->get();

        // Tickets asignados al técnico
        $misTickets = Ticket::with('creadoPor.usuario')
            ->where('asignado_a', $cuentaId)
            ->orderByDesc('id_ticket')
            ->get();

        return view('user.tickets.index', compact('disponibles', 'misTickets'));
    }

    /**
     * Tomar un ticket (auto-asignarse)
     */
    public function tomar(Ticket $ticket)
    {
        if ($ticket->asignado_a || $ticket->estado !== 'nuevo') {
            return back()->with('error', 'Este ticket ya no está disponible.');
        }

        DB::table('tickets')
            ->where('id_ticket', $ticket->id_ticket)
            ->update([
                'asignado_a' => auth()->user()->id_cuenta,
                'estado' => 'asignado',
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Ticket tomado correctamente 🫡');
    }


    public function store(Request $request)
{
    $data = $request->validate([
        'titulo' => 'required|string|max:255',
        'solicitante' => 'required|string|max:100',
        'descripcion' => 'nullable|string',
        'tipo_formato' => 'required|in:a,b,c,d',
    ]);

    $cuenta = auth()->user();

    // Folio simple institucional para ticket (si luego lo haces consecutivo global, lo movemos a servicio)
    $folio = 'TCK-' . now()->format('YmdHis') . '-' . strtoupper($data['tipo_formato']);

$ticket = \App\Models\Ticket::create([
    'folio' => $folio,
    'titulo' => $data['titulo'],
    'solicitante' => $data['solicitante'],
    'descripcion' => $data['descripcion'] ?? null,
    'tipo_formato' => $data['tipo_formato'],
    'estado' => 'nuevo',
    'creado_por' => $cuenta->id_cuenta,
    'asignado_a' => null,
    'asignado_por' => null,
    'id_servicio' => null,
]);

// ✅ DESTINATARIOS: Admin + técnicos (id_rol 1 y 2)
$emails = \App\Models\Cuenta::with('usuario:id_usuario,email')
    ->whereIn('id_rol', [1,2])
    ->get()
    ->pluck('usuario.email')
    ->filter()
    ->unique()
    ->values()
    ->all();

    if (!empty($emails)) {
    Mail::to($emails)->send(new TicketCreadoMail($ticket));
}

return back()->with('Creado', 'Ticket creado correctamente. 📥');
}




    /**
     * Completar ticket → redirige al formato correspondiente
     */
    public function completar(Ticket $ticket)
    {
        if (in_array($ticket->estado, ['cancelado', 'completado'])) {
            return back()->with('error', 'Este ticket no puede completarse.');
        }

        // Crear servicio si no existe (MISMO FLUJO QUE ADMIN)
        if (!$ticket->id_servicio) {
            DB::transaction(function () use ($ticket) {

                $tipo = strtoupper($ticket->tipo_formato);
                $folio = $this->tickets->generarFolioGlobal($tipo);

                $idServicio = DB::table('servicios')->insertGetId([
                    'folio' => $folio,
                    'fecha' => now()->format('Y-m-d'),
                    'id_usuario' => auth()->user()->id_usuario,
                    'id_departamento' => null,
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

                $ticket->id_servicio = $idServicio;
            });
        }

        // Redirección al formato
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
    return view('user.tickets.edit', compact('ticket'));
}

public function update(Request $request, Ticket $ticket)
{
    // 1. Validación de los campos, incluyendo el tipo de formato
    $data = $request->validate([
        'titulo'       => 'required|string|max:255',
        'solicitante'  => 'required|string|max:150',
        'descripcion'  => 'nullable|string|max:200', // Límite de 200 caracteres
        'tipo_formato' => 'required|in:a,b,c,d',    // Permitimos los formatos A, B, C y D
    ]);

    // 2. Ejecutar la actualización según tu lógica de propietario
    $this->tickets->actualizarComoPropietario(auth()->user(), $ticket, $data);

    // 3. Redirección con mensaje de éxito
    return redirect()->route('user.tickets.index')->with('success', 'Ticket actualizado correctamente ✅');
}
}