<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Sistema de Formatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .user-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .permiso-badge {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="user-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-1">Panel de Usuario</h1>
                    <p class="mb-0 opacity-75">Bienvenido al Sistema de Formatos Digitales</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="me-3">Hola, <strong>{{ Auth::user()->usuario->nombre }}</strong></span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Tarjetas de Acceso Rápido -->
        <div class="row mb-4">
            <!-- Gestión de Usuarios -->
            @if(Auth::user()->puedeGestionarUsuarios())
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                        <h5 class="card-title">Ver Usuarios</h5>
                        <p class="card-text text-muted">Consulta la lista de usuarios del sistema</p>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-100">
                            Acceder a Gestión
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Gestión de Formatos -->
            @if(Auth::user()->puedeGestionarFormatos())
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-file-alt fa-2x text-success"></i>
                        </div>
                        <h5 class="card-title">Gestionar Formatos</h5>
                        <p class="card-text text-muted">Administra los formatos del sistema</p>
                        <a href="#" class="btn btn-success w-100">
                            Gestionar Formatos
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Crear Usuarios -->
            @if(Auth::user()->puedeCrearUsuarios())
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-plus fa-2x text-info"></i>
                        </div>
                        <h5 class="card-title">Crear Usuarios</h5>
                        <p class="card-text text-muted">Agregar nuevos usuarios al sistema</p>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-info w-100">
                            Crear Usuario
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Reportes -->
            @if(Auth::user()->puedeGestionarFormatos())
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-chart-bar fa-2x text-warning"></i>
                        </div>
                        <h5 class="card-title">Reportes</h5>
                        <p class="card-text text-muted">Generar reportes del sistema</p>
                        <a href="#" class="btn btn-warning w-100">
                            Ver Reportes
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Información del Usuario -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Mis Permisos Actuales</h5>
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

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Mi Información</h5>
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

        <!-- Mensaje para usuarios sin permisos -->
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

    <!-- Font Awesome para íconos -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>