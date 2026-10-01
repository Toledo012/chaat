@extends('layouts.admin')

@section('title', 'Mi panel | SEMAHN')
@section('header_title', 'Mi panel')
@section('header_subtitle', 'Tickets asignados y actividad personal')

@section('styles')
<style>
    .dashboard-card { border-radius: 1rem; }
    .dashboard-kpi { border-radius: .75rem; }
    .dashboard-kpi h4, .dashboard-highlight { font-variant-numeric: tabular-nums; }
    .dashboard-chart { position: relative; height: 280px; }
    .dashboard-chart-scroll { overflow-x: auto; }
    .dashboard-chart-monthly { min-width: 600px; }
    .dashboard-chart-distribution { height: 215px; }
    .dashboard-period-filter { display: flex; flex-wrap: wrap; align-items: end; gap: .75rem; }
    .dashboard-period-filter .form-select { min-width: 135px; }
    .dashboard-legend-row { display: flex; justify-content: space-between; gap: .75rem; padding: .25rem 0; }
    .dashboard-action { display: flex; align-items: center; gap: .75rem; min-height: 100%; border: 1px solid var(--border-color); border-radius: .75rem; padding: 1rem; color: var(--text-color); }
    .dashboard-action:hover { background: var(--surface-muted); color: var(--primary-color); }
    .dashboard-action i { color: var(--primary-color); font-size: 1.2rem; }
    .ticket-mini-item .ticket-line { display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: .4rem; }
    .profile-detail { display: flex; justify-content: space-between; flex-wrap: wrap; gap: .35rem 1rem; overflow-wrap: anywhere; }
    @media (max-width: 575.98px) {
        .dashboard-chart { height: 245px; }
        .dashboard-chart-distribution { height: 205px; }
        .dashboard-period-filter > div, .dashboard-period-filter .form-select { width: 100%; }
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        background: var(--surface-muted);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 2.5rem;
        margin: 0 auto 15px;
        border: 2px solid var(--primary-color);
    }

    .permiso-chip {
        display: inline-flex;
        padding: 5px 12px;
        border-radius: 20px;
        margin: 3px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .permiso-chip.active { background: var(--surface-muted); border: 1px solid var(--primary-color); color: var(--primary-color); }
    .permiso-chip.inactive { background: var(--surface-muted); border: 1px solid var(--border-color); color: var(--secondary-color); opacity: 0.7; }

    .ticket-mini-item {
        border-left: 4px solid var(--primary-color);
        transition: 0.2s;
    }
    .ticket-mini-item:hover { background-color: var(--surface-muted); }

    .toggle-password {
        position: absolute;
        right: 15px;
        top: 38px;
        cursor: pointer;
        color: var(--secondary-color);
    }
    #password-rules li { font-size: 0.75rem; }
    #password-rules li.ok { color: #28a745; font-weight: bold; }
    
</style>
@endsection

@section('content')
<div class="container-fluid px-2">

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Resumen de tickets asignados y disponibles para tomar. --}}
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card dashboard-kpi p-3 border-0 shadow-sm h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-3 me-3">
                        <i class="fas fa-ticket-alt fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-body-secondary text-uppercase fw-bold" style="font-size: 0.65rem;">Asignados</small>
                        <h4 class="fw-bold mb-0">{{ $ticketStats->total }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card dashboard-kpi p-3 border-0 shadow-sm h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-3 me-3">
                        <i class="fas fa-spinner fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-body-secondary text-uppercase fw-bold" style="font-size: 0.65rem;">Activos</small>
                        <h4 class="fw-bold mb-0">{{ $ticketStats->activos }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card dashboard-kpi p-3 border-0 shadow-sm h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning p-3 rounded-3 me-3">
                        <i class="fas fa-check-double fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-body-secondary text-uppercase fw-bold" style="font-size: 0.65rem;">Completados</small>
                        <h4 class="fw-bold mb-0">{{ $ticketStats->completados }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card dashboard-kpi p-3 border-0 shadow-sm h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-info-subtle text-info p-3 rounded-3 me-3">
                        <i class="fas fa-inbox fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-body-secondary text-uppercase fw-bold" style="font-size: 0.65rem;">Disponibles</small>
                        <h4 class="fw-bold mb-0">{{ $disponibles }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $selectedTickets = $month ? $ticketActivity['current'][$month - 1] : array_sum($ticketActivity['current']);
        $comparisonTickets = $month ? $ticketActivity['previous'][$month - 1] : array_sum($ticketActivity['previous']);
        $formatTypes = ['A', 'B', 'C', 'D'];
        if (($formatosPorTipo['R'] ?? 0) > 0) $formatTypes[] = 'R';
        $formatPalette = ['A' => '#399e91', 'B' => '#65afbd', 'C' => '#c8a457', 'D' => '#9a364d', 'R' => '#788a8b'];
        $formatColors = array_map(fn ($type) => $formatPalette[$type], $formatTypes);
        $formatLabels = array_map(fn ($type) => "Formato {$type}", $formatTypes);
        $formatValues = array_map(fn ($type) => (int) ($formatosPorTipo[$type] ?? 0), $formatTypes);
    @endphp
    <div class="card dashboard-card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h2 class="h6 fw-bold mb-1">Mi actividad</h2>
                <p class="small text-body-secondary mb-0">Tickets asignados por fecha de creación{{ $puedeVerFormatos ? ' · Formatos registrados por fecha del servicio' : '' }}</p>
            </div>
            @include('partials.dashboard-period-filter', ['action' => route('user.dashboard'), 'yearMin' => $ticketActivity['yearMin']])
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="{{ $puedeVerFormatos ? 'col-xl-8' : 'col-12' }}">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <h2 class="h6 fw-bold mb-1"><i class="fas fa-chart-bar me-2 text-primary"></i>Mis tickets por mes</h2>
                            <p class="small text-body-secondary mb-0">{{ $year }} frente a {{ $year - 1 }}{{ $month ? ' · Detalle: ' . $months[$month] : ' · Todo el año' }}</p>
                        </div>
                        <div class="text-md-end">
                            <strong class="dashboard-highlight fs-4 d-block lh-1">{{ number_format($selectedTickets) }}</strong>
                            <small class="text-body-secondary">{{ $month ? 'asignados en ' . strtolower($months[$month]) : 'asignados en ' . $year }}</small>
                        </div>
                    </div>
                    <p class="small text-body-secondary mb-3">{{ $selectedTickets - $comparisonTickets >= 0 ? '+' : '' }}{{ $selectedTickets - $comparisonTickets }} frente al mismo {{ $month ? 'mes' : 'periodo' }} de {{ $year - 1 }}</p>
                    @if(array_sum($ticketActivity['current']) + array_sum($ticketActivity['previous']) > 0)
                        <div class="dashboard-chart-scroll" aria-label="Gráfica desplazable de mis tickets por mes">
                            <div class="dashboard-chart dashboard-chart-monthly">
                                <canvas data-ticket-activity data-current='@json($ticketActivity['current'])' data-previous='@json($ticketActivity['previous'])' data-year="{{ $year }}" data-month="{{ $month }}"></canvas>
                            </div>
                        </div>
                    @else
                        <p class="text-body-secondary text-center py-5 mb-0">Aún no tienes tickets asignados en estos años.</p>
                    @endif
                </div>
            </div>
        </div>
        @if($puedeVerFormatos)
            <div class="col-xl-4">
                <div class="card dashboard-card border-0 shadow-sm h-100">
                    <div class="card-body p-3 p-md-4">
                        <h2 class="h6 fw-bold mb-1"><i class="fas fa-chart-pie me-2 text-primary"></i>Mis formatos</h2>
                        <p class="small text-body-secondary mb-3">{{ $month ? $months[$month] . ' de ' . $year : 'Todo ' . $year }}</p>
                        @if($formatosTotal > 0)
                            <div class="dashboard-chart dashboard-chart-distribution">
                                <canvas data-role-distribution data-labels='@json($formatLabels)' data-values='@json($formatValues)' data-colors='@json($formatColors)'></canvas>
                            </div>
                            <div class="mt-3 small">
                                @foreach($formatTypes as $index => $type)
                                    <div class="dashboard-legend-row">
                                        <span><span class="d-inline-block rounded-circle me-2" style="width:.65rem;height:.65rem;background:{{ $formatColors[$index] }};"></span>Formato {{ $type }}</span>
                                        <span class="fw-semibold">{{ $formatValues[$index] }} <span class="text-body-secondary fw-normal">({{ round($formatValues[$index] * 100 / $formatosTotal) }}%)</span></span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-body-secondary text-center py-5 mb-0">No has registrado formatos en este periodo.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ACCIONES PRINCIPALES --}}
    <div class="row g-3 mb-4">
        @if($puedeVerUsuarios)
            <div class="col-12 col-md-4">
                <a href="{{ route('admin.users.index') }}" class="dashboard-action text-decoration-none"><i class="fas fa-users-cog"></i><span><strong class="d-block">Usuarios</strong><small class="text-body-secondary">Personal y accesos</small></span></a>
            </div>
        @endif

        @if($puedeVerFormatos)
            <div class="col-12 col-md-4">
                <a href="{{ route('admin.formatos.index') }}" class="dashboard-action text-decoration-none"><i class="fas fa-file-signature"></i><span><strong class="d-block">Formatos</strong><small class="text-body-secondary">Servicios técnicos</small></span></a>
            </div>
        @endif

        <div class="col-12 col-md-4">
            <a href="{{ route('user.tickets.index') }}" class="dashboard-action text-decoration-none"><i class="fas fa-clipboard-list"></i><span><strong class="d-block">Bandeja de tickets</strong><small class="text-body-secondary">Seguimiento y disponibles</small></span></a>
        </div>
    </div>

    <div class="row">
        {{-- 🔷 COLUMNA IZQUIERDA: PERFIL --}}
        <div class="col-lg-4 mb-4">
            <div class="card p-4 h-100 border-0 shadow-sm">
                <div class="text-center">
                    <div class="profile-avatar">
                        {{ strtoupper(substr(Auth::user()->usuario->nombre, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-0">{{ Auth::user()->usuario->nombre }}</h5>
                    <p class="text-body-secondary small">{{ Auth::user()->usuario->puesto }}</p>
                </div>

                <div class="mt-3 small">
                    <div class="profile-detail mb-2">
                        <span class="text-body-secondary">Email:</span>
                        <span class="fw-bold text-body">{{ Auth::user()->usuario->email }}</span>
                    </div>
                    <div class="profile-detail mb-2">
                        <span class="text-body-secondary">Departamento:</span>
                        <span class="fw-bold text-body">{{ Auth::user()->usuario->departamentos->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-detail mb-2">
                        <span class="text-body-secondary">Usuario:</span>
                        <span class="badge bg-body-tertiary text-primary border border-primary-subtle">{{ Auth::user()->username }}</span>
                    </div>
                    <div class="profile-detail">
                        <span class="text-body-secondary">Rol:</span>
                        <span class="badge {{ Auth::user()->isAdmin() ? 'bg-danger' : 'bg-primary' }}">
                            {{ Auth::user()->isAdmin() ? 'ADMINISTRADOR' : 'USUARIO' }}
                        </span>
                    </div>
                </div>

                <button class="btn btn-outline-primary btn-sm mt-4 w-100 fw-bold rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="fas fa-key me-1"></i> Actualizar Seguridad
                </button>
            </div>
        </div>

        {{--  TICKETS RECIENTES --}}
        <div class="col-lg-8 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-body">
                        <i class="fas fa-history me-2 text-primary"></i>Actividad Reciente en Tickets
                    </h5>
                    <a href="{{ route('user.tickets.index') }}" class="btn btn-sm btn-link text-primary text-decoration-none fw-bold">Ver bandeja completa</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush px-2">
                        @forelse($misTickets->take(5) as $ticket)
                            <div class="list-group-item ticket-mini-item border-0 border-bottom mb-2 rounded-3 mx-2">
                                <div class="ticket-line mb-1">
                                    <span class="fw-bold text-body small">#{{ $ticket->folio }} - {{ \Illuminate\Support\Str::limit($ticket->titulo, 50) }}</span>
                                    @php
                                        $stColor = match($ticket->estado) {
                                            'nuevo' => 'primary',
                                            'en_proceso' => 'warning',
                                            'completado' => 'success',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $stColor }} rounded-pill" style="font-size: 0.6rem;">{{ strtoupper($ticket->estado) }}</span>
                                </div>
                                <div class="ticket-line">
                                    <small class="text-body-secondary"><i class="fas fa-clock me-1"></i>{{ $ticket->created_at->diffForHumans() }}</small>
                                    <small class="text-body-secondary small">Prioridad: <strong>{{ ucfirst($ticket->prioridad) }}</strong></small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard fa-3x text-body-secondary opacity-25 mb-3"></i>
                                <p class="text-body-secondary small">No tienes tickets asignados recientemente.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--MIS PERMISOS --}}
    <div class="row mt-2">
        <div class="col-12">
            <div class="card p-4 border-0 shadow-sm">
                <h6 class="fw-bold text-body-secondary text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                    <i class="fas fa-shield-alt me-2 text-primary"></i>Privilegios Actuales en el Sistema
                </h6>
                <div class="d-flex flex-wrap">
                    @php
                        $permisos = [
                            'Gestión de Usuarios' => Auth::user()->puedeGestionarUsuarios(),
                            'Creación de Personal' => Auth::user()->puedeCrearUsuarios(),
                            'Gestión de Formatos' => Auth::user()->puedeGestionarFormatos(),
                   
                            'Administración de Roles' => Auth::user()->puedeCambiarRoles(),
                            'Control de Cuentas' => Auth::user()->puedeActivarCuentas(),
                        ];
                    @endphp

                    @foreach($permisos as $nombre => $activo)
                        <div class="permiso-chip {{ $activo ? 'active' : 'inactive' }} shadow-sm">
                            <i class="fas {{ $activo ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $nombre }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>

{{-- 🔒 MODAL CAMBIO DE CONTRASEÑA --}}
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('user.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-shield-lock me-2"></i>Seguridad de Cuenta</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 position-relative">
                        <label class="modal-label-header">Contraseña Actual</label>
                        <input type="password" name="current_password" id="current_password" class="form-control shadow-sm" required>
                        <span class="toggle-password" data-target="current_password"><i class="fas fa-eye"></i></span>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="modal-label-header text-primary">Nueva Contraseña</label>
                        <input type="password" name="password" id="new_password" class="form-control shadow-sm" required>
                        <span class="toggle-password" data-target="new_password"><i class="fas fa-eye"></i></span>

                        <ul class="list-unstyled mt-3 p-3 bg-body-tertiary rounded-3" id="password-rules">
                            <li id="rule-length" class="mb-1 small"><i class="fas fa-circle me-2" style="font-size: 0.5rem;"></i> Mínimo 6 caracteres</li>
                            <li id="rule-upper" class="mb-1 small"><i class="fas fa-circle me-2" style="font-size: 0.5rem;"></i> Al menos una letra mayúscula</li>
                            <li id="rule-number" class="small"><i class="fas fa-circle me-2" style="font-size: 0.5rem;"></i> Al menos un número</li>
                        </ul>
                    </div>

                    <div class="mb-0 position-relative">
                        <label class="modal-label-header">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" id="confirm_password" class="form-control shadow-sm" required>
                        <span class="toggle-password" data-target="confirm_password"><i class="fas fa-eye"></i></span>
                        <small id="matchMessage" class="text-danger d-none mt-1 fw-bold">⚠️ Las contraseñas no coinciden</small>
                    </div>
                </div>
                <div class="modal-footer bg-body-tertiary border-0">
                    <button type="button" class="btn btn-link text-body-secondary text-decoration-none" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/role-dashboard.js') }}"></script>
<script>
// Toggle de visibilidad de contraseña
document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.target);
        input.type = input.type === "password" ? "text" : "password";
        this.querySelector("i").classList.toggle("fa-eye");
        this.querySelector("i").classList.toggle("fa-eye-slash");
    });
});

// Validación en tiempo real de reglas y coincidencia
const pass = document.getElementById("new_password");
const conf = document.getElementById("confirm_password");

function validate() {
    const v = pass.value;
    document.getElementById("rule-length").classList.toggle("ok", v.length >= 6);
    document.getElementById("rule-upper").classList.toggle("ok", /[A-Z]/.test(v));
    document.getElementById("rule-number").classList.toggle("ok", /[0-9]/.test(v));

    const msg = document.getElementById("matchMessage");
    if (pass.value === conf.value && conf.value.length > 0) {
        conf.classList.add("is-valid");
        conf.classList.remove("is-invalid");
        msg.classList.add("d-none");
    } else if (conf.value.length > 0) {
        conf.classList.add("is-invalid");
        conf.classList.remove("is-valid");
        msg.classList.remove("d-none");
    }
}

pass.addEventListener("input", validate);
conf.addEventListener("input", validate);
</script>
@endsection
