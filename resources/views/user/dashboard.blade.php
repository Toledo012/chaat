@extends('layouts.admin')

@section('title', 'Panel de Usuario - Sistema de Formatos')
@section('header_title', 'Panel de Usuario')
@section('header_subtitle', 'Bienvenido al Sistema de Formatos Digitales')

@section('styles')
<style>
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: 0.3s;
    }
    .card:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }

    .profile-avatar {
        width: 80px;
        height: 80px;
        background: #f0f7f6;
        color: #399e91;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 2.5rem;
        margin: 0 auto 15px;
        border: 2px solid #399e91;
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
    .permiso-chip.active { background: #e8f5e9; border: 1px solid #2e7d32; color: #1b5e20; }
    .permiso-chip.inactive { background: #f5f5f5; border: 1px solid #bdbdbd; color: #757575; opacity: 0.7; }

    .ticket-mini-item {
        border-left: 4px solid #399e91;
        transition: 0.2s;
    }
    .ticket-mini-item:hover { background-color: #f8fafc; }

    .toggle-password {
        position: absolute;
        right: 15px;
        top: 38px;
        cursor: pointer;
        color: #666;
    }
    #password-rules li { font-size: 0.75rem; }
    #password-rules li.ok { color: #28a745; font-weight: bold; }
    
    .stat-box {
        text-align: center;
        padding: 15px;
        border-radius: 12px;
        background: #f8fbff;
    }
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

    {{-- 📊 RESUMEN RÁPIDO (IDEA EXTRA) --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card p-3 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-3 me-3">
                        <i class="fas fa-ticket-alt fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Mis Tickets</small>
                        <h4 class="fw-bold mb-0">{{ $misTickets->count() ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-3 me-3">
                        <i class="fas fa-check-double fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Finalizados</small>
                        <h4 class="fw-bold mb-0">{{ $misTickets->where('estado', 'completado')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning p-3 rounded-3 me-3">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">En Proceso</small>
                        <h4 class="fw-bold mb-0">{{ $misTickets->where('estado', 'en_proceso')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-info-subtle text-info p-3 rounded-3 me-3">
                        <i class="fas fa-file-invoice fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Formatos Disp.</small>
                        <h4 class="fw-bold mb-0">4</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ACCIONES PRINCIPALES --}}
    <div class="row mb-4">
        @if(Auth::user()->puedeGestionarUsuarios())
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="card text-center p-3 border-0">
                    <i class="fas fa-users-cog fa-2x text-primary mb-2"></i>
                    <h6 class="fw-bold text-dark">Gestión de Usuarios</h6>
                    <small class="text-muted">Control de personal y accesos.</small>
                </div>
            </a>
        </div>
        @endif

        @if(Auth::user()->puedeGestionarFormatos())
        <div class="col-md-4 mb-3">
            <a href="{{ route('admin.formatos.index') }}" class="text-decoration-none">
                <div class="card text-center p-3 border-0">
                    <i class="fas fa-file-signature fa-2x text-success mb-2"></i>
                    <h6 class="fw-bold text-dark">Módulo de Formatos</h6>
                    <small class="text-muted">Registro de servicios técnicos.</small>
                </div>
            </a>
        </div>
        @endif

        <div class="col-md-4 mb-3">
            <a href="{{ route('user.tickets.index') }}" class="text-decoration-none">
                <div class="card text-center p-3 border-0">
                    <i class="fas fa-clipboard-list fa-2x text-info mb-2"></i>
                    <h6 class="fw-bold text-dark">Mis Tickets</h6>
                    <small class="text-muted">Bandeja de seguimiento personal.</small>
                </div>
            </a>
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
                    <p class="text-muted small">{{ Auth::user()->usuario->puesto }}</p>
                </div>

                <div class="mt-3 small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Email:</span>
                        <span class="fw-bold text-dark">{{ Auth::user()->usuario->email }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Departamento:</span>
                        <span class="fw-bold text-dark">{{ Auth::user()->usuario->departamentos->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Usuario:</span>
                        <span class="badge bg-light text-primary border border-primary-subtle">{{ Auth::user()->username }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Rol:</span>
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

        {{-- 🔷 COLUMNA DERECHA: TICKETS RECIENTES --}}
        <div class="col-lg-8 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-history me-2 text-primary"></i>Actividad Reciente en Tickets
                    </h5>
                    <a href="{{ route('user.tickets.index') }}" class="btn btn-sm btn-link text-primary text-decoration-none fw-bold">Ver bandeja completa</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush px-2">
                        @forelse($misTickets->take(5) as $ticket)
                            <div class="list-group-item ticket-mini-item border-0 border-bottom mb-2 rounded-3 mx-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">#{{ $ticket->folio }} - {{ \Illuminate\Support\Str::limit($ticket->titulo, 50) }}</span>
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
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $ticket->created_at->diffForHumans() }}</small>
                                    <small class="text-muted small">Prioridad: <strong>{{ ucfirst($ticket->prioridad) }}</strong></small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard fa-3x text-muted opacity-25 mb-3"></i>
                                <p class="text-muted small">No tienes tickets asignados o creados recientemente.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔷 ABAJO: MIS PERMISOS --}}
    <div class="row mt-2">
        <div class="col-12">
            <div class="card p-4 border-0 shadow-sm">
                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
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

                        <ul class="list-unstyled mt-3 p-3 bg-light rounded-3" id="password-rules">
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
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
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