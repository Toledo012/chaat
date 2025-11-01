@extends('layouts.admin')

@section('title', 'Panel de Usuario - Sistema de Formatos')
@section('header_title', 'Panel de Usuario')
@section('header_subtitle', 'Bienvenido al Sistema de Formatos Digitales')

@section('styles')
<style>
    .card {
        transition: transform 0.2s;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .permiso-badge {
        font-size: 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        {{-- GESTIÓN DE USUARIOS --}}
        @if(Auth::user()->puedeGestionarUsuarios())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-primary mb-3"></i>
                    <h5 class="card-title">Ver Usuarios</h5>
                    <p class="card-text text-muted">Consulta la lista de usuarios del sistema</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-100">Acceder a Gestión</a>
                </div>
            </div>
        </div>
        @endif

        {{-- GESTIÓN DE FORMATOS --}}
        @if(Auth::user()->puedeGestionarFormatos())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-file-alt fa-2x text-success mb-3"></i>
                    <h5 class="card-title">Gestionar Formatos</h5>
                    <p class="card-text text-muted">Administra los formatos del sistema</p>
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-success w-100">Ir a Formatos</a>
                </div>
            </div>
        </div>
        @endif

        {{-- CREAR USUARIOS --}}
        @if(Auth::user()->puedeCrearUsuarios())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-user-plus fa-2x text-info mb-3"></i>
                    <h5 class="card-title">Crear Usuarios</h5>
                    <p class="card-text text-muted">Agregar nuevos usuarios al sistema</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-info w-100">Crear Usuario</a>
                </div>
            </div>
        </div>
        @endif

        {{-- REPORTES --}}
        @if(Auth::user()->puedeGestionarFormatos())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-chart-bar fa-2x text-warning mb-3"></i>
                    <h5 class="card-title">Reportes</h5>
                    <p class="card-text text-muted">Generar reportes del sistema</p>
                    <a href="#" class="btn btn-warning w-100 text-dark">Ver Reportes</a>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- INFORMACIÓN DEL USUARIO --}}
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Mis Permisos Actuales</h5>
                </div>
                <div class="card-body">
                    @php
                        $permisosActivos = [
                            'gestion_usuarios' => Auth::user()->puedeGestionarUsuarios(),
                            'gestion_formatos' => Auth::user()->puedeGestionarFormatos(),
                            'crear_usuarios' => Auth::user()->puedeCrearUsuarios(),
                            'editar_usuarios' => Auth::user()->puedeEditarUsuarios(),
                            'eliminar_usuarios' => Auth::user()->puedeEliminarUsuarios(),
                            'cambiar_roles' => Auth::user()->puedeCambiarRoles(),
                            'activar_cuentas' => Auth::user()->puedeActivarCuentas(),
                        ];

                        $permisosNombres = [
                            'gestion_usuarios' => 'Ver Usuarios',
                            'gestion_formatos' => 'Gestionar Formatos',
                            'crear_usuarios' => 'Crear Usuarios',
                            'editar_usuarios' => 'Editar Usuarios',
                            'eliminar_usuarios' => 'Eliminar Usuarios',
                            'cambiar_roles' => 'Cambiar Roles',
                            'activar_cuentas' => 'Activar Cuentas',
                        ];
                    @endphp

                    <div class="row">
                        @foreach($permisosActivos as $permiso => $activo)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge {{ $activo ? 'bg-success' : 'bg-secondary' }} permiso-badge me-2">
                                    {{ $activo ? '✅' : '❌' }}
                                </span>
                                <span class="{{ $activo ? 'text-dark' : 'text-muted' }}">
                                    {{ $permisosNombres[$permiso] }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- MI INFORMACIÓN --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Mi Información</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nombre:</strong><br>{{ Auth::user()->usuario->nombre }}</p>
                    <p><strong>Email:</strong><br>{{ Auth::user()->usuario->email }}</p>
                    <p><strong>Departamento:</strong><br>{{ Auth::user()->usuario->departamento }}</p>
                    <p><strong>Puesto:</strong><br>{{ Auth::user()->usuario->puesto }}</p>
                    <p><strong>Usuario:</strong><br>{{ Auth::user()->username }}</p>
                    <p><strong>Rol:</strong><br>
                        <span class="badge {{ Auth::user()->isAdmin() ? 'bg-danger' : 'bg-primary' }}">
                            {{ Auth::user()->isAdmin() ? 'Administrador' : 'Usuario' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ACCESO LIMITADO --}}
    @if(!Auth::user()->puedeGestionarUsuarios() && !Auth::user()->puedeGestionarFormatos() && !Auth::user()->isAdmin())
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <h5>Acceso Limitado</h5>
                <p class="mb-2">Tu cuenta tiene acceso básico al sistema. Si necesitas permisos adicionales, contacta al administrador.</p>
                <ul class="mb-0">
                    <li>Puedes ver tu información personal</li>
                    <li>Puedes cambiar tu contraseña</li>
                    <li>Puedes acceder a formatos asignados</li>
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Animación suave de tarjetas
    document.querySelectorAll('.card').forEach((card, i) => {
        card.style.opacity = 0;
        card.style.transform = 'translateY(15px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = 1;
            card.style.transform = 'translateY(0)';
        }, i * 100);
    });
</script>
@endsection
