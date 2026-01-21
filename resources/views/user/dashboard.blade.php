@extends('layouts.admin')

@section('title', 'Panel de Usuario - Sistema de Formatos')
@section('header_title', 'Panel de Usuario')
@section('header_subtitle', 'Bienvenido al Sistema de Formatos Digitales')

@section('styles')
<style>
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: 0.2s;
    }
    .card:hover { transform: translateY(-4px); }

    .profile-icon { font-size: 60px; color: #399e91; }

    .permiso-chip {
        display: inline-flex;
        padding: 6px 12px;
        border-radius: 20px;
        margin: 4px;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .permiso-chip.active {
        background: #28a74522;
        border: 1px solid #28a745;
        color: #155724;
    }
    .permiso-chip.inactive {
        background: #cccccc33;
        border: 1px solid #999;
        color: #666;
    }

    .toggle-password {
        position: absolute;
        right: 15px;
        top: 38px;
        cursor: pointer;
        color: #666;
    }
    .toggle-password:hover { color: #000; }

    #password-rules li.ok { color: green; font-weight: bold; }
    #confirm_password.ok { border-color: green !important; }

    .modern-table thead { background: #f8f9fa; }
    .modern-table tbody tr:hover {
        background: #eef7f5 !important;
        transform: scale(1.01);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TARJETAS PRINCIPALES --}}
    <div class="row mb-4">

        @if(Auth::user()->puedeGestionarUsuarios())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card text-center p-4">
                <i class="fas fa-users fa-2x text-primary mb-3"></i>
                <h5 class="fw-bold">Ver Usuarios</h5>
                <p class="text-muted small">Consulta la lista completa del sistema.</p>
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-100">Acceder</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->puedeGestionarFormatos())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card text-center p-4">
                <i class="fas fa-file-alt fa-2x text-success mb-3"></i>
                <h5 class="fw-bold">Gestionar Formatos</h5>
                <p class="text-muted small">Administra registros y reportes.</p>
                <a href="{{ route('admin.formatos.index') }}" class="btn btn-success w-100">Ir a Formatos</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->puedeCrearUsuarios())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card text-center p-4">
                <i class="fas fa-user-plus fa-2x text-info mb-3"></i>
                <h5 class="fw-bold">Crear Usuarios</h5>
                <p class="text-muted small">Agregar personal nuevo al sistema.</p>
                <a href="{{ route('admin.users.index') }}" class="btn btn-info w-100">Crear Usuario</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->puedeGestionarFormatos())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card text-center p-4">
                <i class="fas fa-chart-bar fa-2x text-warning mb-3"></i>
                <h5 class="fw-bold">Reportes</h5>
                <p class="text-muted small">Estadísticas globales del sistema.</p>
                <a href="{{ route('admin.formatos.reporte.general') }}" class="btn btn-warning text-dark w-100">Ver Reportes</a>
            </div>
        </div>
        @endif

    </div>

    <div class="row">

        {{-- 🔷 MI INFORMACIÓN --}}
        <div class="col-lg-4 mb-4">
            <div class="card p-4">

                <div class="text-center mb-2">
                    <i class="fas fa-user-circle profile-icon mb-2"></i>
                    <h4 class="fw-bold">{{ Auth::user()->usuario->nombre }}</h4>
                    <p class="text-muted">{{ Auth::user()->usuario->puesto }}</p>
                </div>

                <hr>

                <p><strong>Email:</strong><br>{{ Auth::user()->usuario->email }}</p>
                <p><strong>Departamento:</strong><br>{{ Auth::user()->usuario->departamento }}</p>
                <p><strong>Usuario:</strong><br>{{ Auth::user()->username }}</p>

                <p><strong>Rol:</strong><br>
                    <span class="badge {{ Auth::user()->isAdmin() ? 'bg-danger' : 'bg-primary' }}">
                        {{ Auth::user()->isAdmin() ? 'Administrador' : 'Usuario' }}
                    </span>
                </p>

                <button class="btn btn-outline-primary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="fas fa-key me-1"></i> Editar Contraseña
                </button>
            </div>
        </div>

        <a href="{{ route('tickets.index') }}" class="text-decoration-none">
    <div class="card shadow-sm border-0 hover-shadow">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:48px;height:48px;background:#399e91;color:white;">
                <i class="fa-solid fa-ticket"></i>
            </div>

            <div>
                <div class="fw-semibold text-dark">Tickets</div>
                <small class="text-muted">Crear y consultar solicitudes</small>
            </div>

            <div class="ms-auto text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </div>
        </div>
    </div>
</a>

<style>
.hover-shadow:hover { box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important; transform: translateY(-1px); transition: .15s; }
</style>


        {{-- 🔷 MATERIALES --}}
        <div class="col-lg-8 mb-4">
            <div class="card p-4">

                <h4 class="text-success fw-bold mb-3">
                    <i class="fa-solid fa-boxes-stacked me-2"></i> Últimos Materiales Añadidos
                </h4>

                @if($materiales->isEmpty())
                    <p class="text-muted">No hay materiales registrados.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover modern-table align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th class="text-center">Unidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materiales as $m)
                            <tr>
                                <td class="fw-semibold">{{ $m->nombre }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark px-3 py-2 rounded-pill">
                                        {{ $m->unidad_sugerida ?? '—' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                <a href="{{ route('admin.materiales.index') }}" class="btn btn-success mt-3 w-100">
                    <i class="fa-solid fa-arrow-right me-1"></i> Ver todos los materiales
                </a>
            </div>
        </div>

    </div>

    {{-- 🔷 MIS PERMISOS (ABIJO COMO PEDISTE) --}}
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card p-4">
                <h4 class="text-primary fw-bold mb-3">
                    <i class="fas fa-shield-alt me-2"></i> Mis Permisos Actuales
                </h4>

                @php
                    $permisos = [
                        'Gestionar Usuarios' => Auth::user()->puedeGestionarUsuarios(),
                        'Gestionar Formatos' => Auth::user()->puedeGestionarFormatos(),
                        'Crear Usuarios' => Auth::user()->puedeCrearUsuarios(),
                        'Editar Usuarios' => Auth::user()->puedeEditarUsuarios(),
                        'Eliminar Usuarios' => Auth::user()->puedeEliminarUsuarios(),
                        'Cambiar Roles' => Auth::user()->puedeCambiarRoles(),
                        'Activar Cuentas' => Auth::user()->puedeActivarCuentas(),

                    ];
                @endphp

                <div class="mt-2">
                    @foreach($permisos as $nombre => $activo)
                        <span class="permiso-chip {{ $activo ? 'active' : 'inactive' }}">
                            <i class="fas {{ $activo ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $nombre }}
                        </span>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

</div>

{{-- 🔒 MODAL CAMBIO DE CONTRASEÑA --}}
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('user.update-password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header bg-primary text-white">
                    <h5><i class="fas fa-lock me-2"></i>Cambiar Contraseña</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- ACTUAL --}}
                    <div class="mb-3 position-relative">
                        <label>Contraseña actual</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                        <span class="toggle-password" data-target="current_password"><i class="fas fa-eye"></i></span>
                    </div>

                    {{-- NUEVA --}}
                    <div class="mb-3 position-relative">
                        <label>Nueva contraseña</label>
                        <input type="password" name="password" id="new_password" class="form-control" required>
                        <span class="toggle-password" data-target="new_password"><i class="fas fa-eye"></i></span>

                        <ul class="text-muted small mt-2" id="password-rules">
                            <li id="rule-length">• Mínimo 6 caracteres</li>
                            <li id="rule-upper">• Al menos una mayúscula</li>
                            <li id="rule-number">• Al menos un número</li>
                        </ul>
                    </div>

                    {{-- CONFIRMAR --}}
                    <div class="mb-3 position-relative">
                        <label>Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" id="confirm_password" class="form-control" required>
                        <span class="toggle-password" data-target="confirm_password"><i class="fas fa-eye"></i></span>
                        <small id="matchMessage" class="text-danger d-none">Las contraseñas no coinciden</small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Toggle de contraseña
document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.target);
        input.type = input.type === "password" ? "text" : "password";
        this.querySelector("i").classList.toggle("fa-eye");
        this.querySelector("i").classList.toggle("fa-eye-slash");
    });
});

// Validaciones
const pass = document.getElementById("new_password");
const conf = document.getElementById("confirm_password");

function checkRules() {
    const v = pass.value;
    document.getElementById("rule-length").classList.toggle("ok", v.length >= 6);
    document.getElementById("rule-upper").classList.toggle("ok", /[A-Z]/.test(v));
    document.getElementById("rule-number").classList.toggle("ok", /[0-9]/.test(v));
}

function matchPasswords() {
    const msg = document.getElementById("matchMessage");
    if (pass.value === conf.value && conf.value.length > 0) {
        conf.classList.add("ok");
        msg.classList.add("d-none");
    } else {
        conf.classList.remove("ok");
        msg.classList.remove("d-none");
    }
}

pass.addEventListener("input", checkRules);
conf.addEventListener("input", matchPasswords);
</script>
@endsection
