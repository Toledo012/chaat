<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Cuenta;
use App\Models\CatalogoMateriales;
use App\Models\Ticket;
use App\Models\Servicio;
use App\Models\Departamento;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isAdmin()) {
            return redirect()->route('user.dashboard');
        }

        $anioActual = now()->year;
        $request->validate([
            'anio' => "nullable|integer|between:2000,{$anioActual}",
            'mes' => 'nullable|integer|between:0,12',
        ]);
        $anio = (int) $request->input('anio', $anioActual);
        $mes = $request->has('mes')
            ? (int) $request->input('mes')
            : ($anio === $anioActual ? now()->month : 0);
        $mesReferencia = $mes ?: ($anio === $anioActual ? now()->month : 12);

        // ── KPIs ──────────────────────────────────────────────
        $stats = [
            'total_usuarios'   => DB::table('usuarios_formatos')->count(),
            'total_servicios'  => DB::table('servicios')->count(),
            'cuentas_activas'  => DB::table('cuentas')->where('estado', 'activo')->count(),
            'tickets_abiertos' => DB::table('tickets')->whereNotIn('estado', ['completado', 'cancelado'])->count(),
        ];

        // ── Últimos 5 materiales ───────────────────────────────
        $materiales = \App\Models\CatalogoMateriales::orderBy('id_material', 'desc')->limit(5)->get();

        // ── Mini bandeja tickets ───────────────────────────────
        $ticketsRecientes = \App\Models\Ticket::orderBy('created_at', 'desc')->limit(5)->get();

        // ── Distribución formatos (gráfica doughnut) ───────────
        $queryFormatos = DB::table('servicios')->whereYear('fecha', $anio);
        if ($mes > 0) {
            $queryFormatos->whereMonth('fecha', $mes);
        }
        $formatosPorTipo = $queryFormatos
            ->select('tipo_formato', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_formato')
            ->pluck('total', 'tipo_formato');
        $tiposFormato = ['A', 'B', 'C', 'D'];
        if (($formatosPorTipo['R'] ?? 0) > 0) {
            $tiposFormato[] = 'R';
        }
        $totalFormatos = $formatosPorTipo->sum();
        $etiquetasFormato = array_map(fn ($tipo) => "Formato {$tipo}", $tiposFormato);
        $datosFormato = array_map(fn ($tipo) => (int) ($formatosPorTipo[$tipo] ?? 0), $tiposFormato);
        $coloresFormato = array_map(fn ($tipo) => [
            'A' => '#399e91', 'B' => '#65afbd', 'C' => '#c8a457',
            'D' => '#9a364d', 'R' => '#788a8b',
        ][$tipo], $tiposFormato);

        $primerTicket = DB::table('tickets')->min('created_at');
        $primerServicio = DB::table('servicios')->min('fecha');
        $aniosConDatos = array_filter([
            $primerTicket ? Carbon::parse($primerTicket)->year : null,
            $primerServicio ? Carbon::parse($primerServicio)->year : null,
        ]);
        $anioMin = max(2000, min($anio, ...($aniosConDatos ?: [$anioActual])));

        // Dos años de tickets en una sola lectura, agrupados por mes sin SQL específico de un motor.
        $ticketsPorMes = [
            $anio - 1 => array_fill(1, 12, 0),
            $anio => array_fill(1, 12, 0),
        ];
        $desde = Carbon::create($anio - 1, 1, 1)->startOfDay();
        $hasta = Carbon::create($anio, 12, 31)->endOfDay();
        foreach (DB::table('tickets')->whereBetween('created_at', [$desde, $hasta])->select('created_at')->cursor() as $ticket) {
            $fecha = Carbon::parse($ticket->created_at);
            $ticketsPorMes[$fecha->year][$fecha->month]++;
        }
        $ticketsMes = $ticketsPorMes[$anio][$mesReferencia];
        $ticketsMesAnterior = $mesReferencia === 1
            ? $ticketsPorMes[$anio - 1][12]
            : $ticketsPorMes[$anio][$mesReferencia - 1];
        $ticketsMismoMesAnterior = $ticketsPorMes[$anio - 1][$mesReferencia];
        $ticketsActuales = array_values($ticketsPorMes[$anio]);
        $ticketsAnteriores = array_values($ticketsPorMes[$anio - 1]);
        $ticketsAnio = array_sum($ticketsActuales);
        $ticketsAnioAnterior = array_sum($ticketsAnteriores);

        // ── Productividad del equipo (top 5) ───────────────────
        $maxServicios = DB::table('usuarios_formatos')
            ->leftJoin('servicios', 'usuarios_formatos.id_usuario', '=', 'servicios.id_usuario')
            ->selectRaw('COUNT(servicios.id_servicio) as total')
            ->groupBy('usuarios_formatos.id_usuario')
            ->orderByDesc('total')
            ->limit(1)
            ->value('total') ?? 1;

        $usuariosFormatos = DB::table('usuarios_formatos')
            ->leftJoin('servicios', 'usuarios_formatos.id_usuario', '=', 'servicios.id_usuario')
            ->select(
                'usuarios_formatos.nombre',
                DB::raw('COUNT(servicios.id_servicio) as total'),
                DB::raw("SUM(CASE WHEN servicios.tipo_formato = 'A' THEN 1 ELSE 0 END) as A"),
                DB::raw("SUM(CASE WHEN servicios.tipo_formato = 'B' THEN 1 ELSE 0 END) as B"),
                DB::raw("SUM(CASE WHEN servicios.tipo_formato = 'C' THEN 1 ELSE 0 END) as C"),
                DB::raw("SUM(CASE WHEN servicios.tipo_formato = 'D' THEN 1 ELSE 0 END) as D")
            )
            ->groupBy('usuarios_formatos.id_usuario', 'usuarios_formatos.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'materiales',
            'ticketsRecientes',
            'formatosPorTipo',
            'tiposFormato',
            'totalFormatos',
            'etiquetasFormato',
            'datosFormato',
            'coloresFormato',
            'anioMin',
            'ticketsPorMes',
            'ticketsActuales',
            'ticketsAnteriores',
            'ticketsAnio',
            'ticketsAnioAnterior',
            'ticketsMes',
            'ticketsMesAnterior',
            'ticketsMismoMesAnterior',
            'anio',
            'mes',
            'mesReferencia',
            'usuariosFormatos',
            'maxServicios'
        ));
    }
    public function usersIndex(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        $puedeVerGestion = $user->isAdmin() ||
            $user->puedeGestionarUsuarios() ||
            $user->puedeCrearUsuarios() ||
            $user->puedeEditarUsuarios() ||
            $user->puedeEliminarUsuarios() ||
            $user->puedeCambiarRoles() ||
            $user->puedeActivarCuentas();
        if (!$puedeVerGestion) {
            return redirect()->route('user.dashboard');
        }
        $usuarios = Usuario::with(['cuenta', 'departamentos'])->get();
        $departamentos = Departamento::orderBy('nombre')->get();

        return view('admin.users.index', compact('usuarios', 'departamentos'));
    }

    public function updateUserRole(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeCambiarRoles())) {
            return redirect()->route('user.dashboard');
        }

        $usuario = Usuario::find($id);

        if ($usuario && $usuario->cuenta) {
            if ($usuario->id_usuario == 1) {
                return redirect()->route('admin.users.index')->with('error', 'El Super Admin no puede ser modificado.');
            }

            if ($usuario->cuenta->id_rol == 1 && auth()->user()->id_usuario != 1) {
                return redirect()->route('admin.users.index')->with('error', 'Solo el Super Admin puede cambiar el rol de un Administrador.');
            }

            if ($usuario->id_usuario == auth()->user()->id_usuario) {
                return redirect()->route('admin.users.index')->with('error', 'No puedes cambiar tu propio rol.');
            }

            if ($request->rol == 1 && auth()->user()->id_usuario != 1) {
                return redirect()->route('admin.users.index')->with('error', 'Solo el Super Admin puede asignar el rol de Administrador.');
            }

            $usuario->cuenta->update(['id_rol' => $request->rol]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Rol actualizado correctamente');
    }

    public function updateUserPermissions(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeCambiarRoles())) {
            return redirect()->route('user.dashboard');
        }

        $usuario = Usuario::find($id);

        if ($usuario && $usuario->cuenta) {
            if ($usuario->cuenta->id_rol == 1 && auth()->user()->id_usuario != 1) {
                return redirect()->route('admin.users.index')->with('error', 'Solo el Super Admin puede editar a un Administrador.');
            }

            if ($usuario->id_usuario == auth()->user()->id_usuario) {
                return redirect()->route('admin.users.index')->with('error', 'No puedes cambiar tu propio permiso.');
            }

            $permisos = $request->input('permisos', []);
            $usuario->cuenta->actualizarPermisos($permisos);

            if (auth()->user()->id_cuenta == $usuario->cuenta->id_cuenta) {
                $nuevosPermisos = $usuario->cuenta->permisosArray();
                session(['permisos_usuario' => $nuevosPermisos]);
            }

            return redirect()->route('admin.users.index')->with('success', 'Permisos actualizados correctamente');
        }

        return redirect()->route('admin.users.index')->with('error', 'Error al actualizar permisos');
    }

    public function toggleUserStatus(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeActivarCuentas())) {
            return redirect()->route('user.dashboard');
        }

        $usuario = Usuario::find($id);

        if ($usuario && $usuario->cuenta) {
            if ($usuario->id_usuario == auth()->user()->id_usuario) {
                return redirect()->route('admin.users.index')->with('error', 'No puedes desactivar tu propia cuenta.');
            }

            if ($usuario->cuenta->id_rol == 1 && auth()->user()->id_usuario != 1) {
                return redirect()->route('admin.users.index')->with('error', 'Solo el Super Admin puede activar o desactivar Administradores.');
            }

            $nuevoEstado = $usuario->cuenta->estado == 'activo' ? 'inactivo' : 'activo';
            $usuario->cuenta->update(['estado' => $nuevoEstado]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Estado de cuenta actualizado');
    }

    public function updateUser(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeEditarUsuarios())) {
            return redirect()->route('user.dashboard');
        }

        $usuario = Usuario::find($id);

        if (!$usuario) {
            return redirect()->route('admin.users.index')->with('error', 'Usuario no encontrado');
        }

        if ($usuario->cuenta && $usuario->cuenta->id_rol == 1 && auth()->user()->id_usuario != 1) {
            return redirect()->route('admin.users.index')->with('error', 'Solo el Super Admin puede editar Administradores.');
        }

        if ($usuario) {
            $usernameRule = 'nullable|string|max:30|unique:cuentas,username';
            if ($usuario->cuenta) {
                $usernameRule = 'nullable|string|max:30|unique:cuentas,username,' . $usuario->cuenta->id_cuenta . ',id_cuenta';
            }

            $request->validate([
                'nombre' => 'required|string|max:50',
                'id_departamento' => 'required|exists:departamentos,id_departamento',
                'puesto' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:50|unique:usuarios_formatos,email,' . $id . ',id_usuario',
                'username' => $usernameRule,
            ]);

            $usuario->update([
                'nombre' => $request->nombre,
                'id_departamento' => $request->id_departamento,
                'puesto' => $request->puesto,
                'extension' => $request->extension ?: null,
                'email' => $request->email,
            ]);

            if ($usuario->cuenta && $request->username) {
                $usuario->cuenta->update(['username' => $request->username]);
            }

            // Fase 4: preferencias de correo (opt-out). Las casillas del modal de
            // edición siempre viajan; una casilla desmarcada = false (no envía).
            if ($usuario->cuenta) {
                $usuario->cuenta->update([
                    'preferencias_correo' => [
                        'nuevos'     => $request->boolean('pref_nuevos'),
                        'asignados'  => $request->boolean('pref_asignados'),
                        'concluidos' => $request->boolean('pref_concluidos'),
                    ],
                ]);
            }

            return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente');
        }

        return redirect()->route('admin.users.index')->with('error', 'Usuario no encontrado');
    }

    public function resetPassword(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return redirect()->route('user.dashboard');
        }

        $usuario = Usuario::findOrFail($id);
        if ($usuario->cuenta) {
            if ($usuario->id_usuario == 1 && auth()->user()->id_usuario != 1) {
                return redirect()->back()->with('error', 'No puedes resetear la clave del Super Admin.');
            }

            $passwordPredeterminada = '$EMAHN2026';
            $usuario->cuenta->update(['password' => Hash::make($passwordPredeterminada)]);

            return redirect()->back()->with('success', "Contraseña de {$usuario->nombre} restablecida a: {$passwordPredeterminada}");
        }
        return redirect()->back()->with('error', 'El usuario no tiene cuenta vinculada.');
    }

    public function createUserAccount(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeCrearUsuarios())) {
            return redirect()->route('user.dashboard');
        }

        $usuario = Usuario::find($id);

        if ($usuario) {
            Cuenta::create([
                'username' => strtolower(str_replace(' ', '.', $usuario->nombre)),
                'password' => Hash::make('$EMAHN2026'),
                'estado' => 'activo',
                'id_usuario' => $usuario->id_usuario,
                'id_rol' => 2
            ]);

            return redirect()->route('admin.users.index')->with('success', 'Cuenta creada. Password por defecto: $EMAHN2026');
        }

        return redirect()->route('admin.users.index')->with('error', 'No se pudo crear la cuenta');
    }

    public function storeUser(Request $request)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeCrearUsuarios())) {
            return redirect()->route('user.dashboard');
        }

        $request->validate([
            'nombre' => 'required|string|max:50',
            'username' => 'required|string|unique:cuentas,username',
            'password' => 'required|string|min:6',
            'id_departamento' => 'required|exists:departamentos,id_departamento',
            'rol' => 'required|in:1,2,3' // 4 es Departamento
        ]);

            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'id_departamento' => $request->id_departamento,
                'puesto' => $request->puesto    ,
'extension' => $request->extension ?: null,
                'email' => $request->email
            ]);

        Cuenta::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'estado' => 'activo',
            'id_usuario' => $usuario->id_usuario,
            'id_rol' => $request->rol
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario y cuenta creados exitosamente');
    }

    public function destroyUser(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeEliminarUsuarios())) {
            return redirect()->route('user.dashboard');
        }

        $usuario = Usuario::find($id);

        if ($usuario) {
            if (auth()->user()->id_usuario == $usuario->id_usuario) {
                return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar tu propia cuenta');
            }

            if ($usuario->cuenta && $usuario->cuenta->id_rol == 1 && auth()->user()->id_usuario != 1) {
                return redirect()->route('admin.users.index')->with('error', 'Solo el Super Admin puede eliminar a un Administrador.');
            }

            if ($usuario->cuenta) { $usuario->cuenta->delete(); }
            $usuario->delete();

            return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado exitosamente');
        }

        return redirect()->route('admin.users.index')->with('error', 'Usuario no encontrado');
    }

    public function redirectUserPermissions(Request $request, $id)
    {
        if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->puedeCambiarRoles())) {
            return redirect()->route('user.dashboard');
        }

        return redirect()->route('admin.users.index')->with('open_permissions_id', (int) $id);
    }
}
