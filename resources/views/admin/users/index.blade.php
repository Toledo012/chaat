<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Formatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            background: #2c3e50;
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            margin: 2px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #34495e;
            color: #3498db;
        }
        .permisos-badge {
            font-size: 0.7rem;
            margin: 1px;
        }
        .permisos-modal .modal-dialog {
            max-width: 320px;
        }
        .form-check {
            margin-bottom: 8px;
            padding-left: 0;
        }
        .form-check-input {
            margin-right: 8px;
        }
        .btn-permisos {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .admin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 1.5rem 0;
        }
        .table-actions {
            min-width: 200px;
        }
    </style>
</head>
<body>
    <!-- Header Admin -->
    <div class="admin-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users-cog me-2"></i>
                        @if(Auth::user()->isAdmin())
                            Gestión de Usuarios - Administrador
                        @else
                            Lista de Usuarios
                        @endif
                    </h1>
                </div>
                <div class="col-md-6 text-end">
                    <span class="me-3">
                        <i class="fas fa-user me-1"></i>
                        {{ Auth::user()->usuario->nombre }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        @if(Auth::user()->isAdmin())
                            <h6 class="text-warning"><i class="fas fa-crown me-1"></i>MENÚ ADMIN</h6>
                        @else
                            <h6 class="text-info">MENÚ USUARIO</h6>
                        @endif
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users me-2"></i>Usuarios
                            </a>
                        </li>
                        @if(Auth::user()->puedeGestionarFormatos())
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-file-alt me-2"></i>Formatos
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar me-2"></i>Reportes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cogs me-2"></i>Configuración
                            </a>
                        </li>
                        @endif
                    </ul>

                    <!-- Info de permisos -->
                    <div class="mt-4 p-3 bg-dark rounded">
                        <small class="text-muted">Tus permisos:</small>
                        <div class="mt-2">
                            @if(Auth::user()->puedeCrearUsuarios())
                                <span class="badge bg-success mb-1">Crear</span>
                            @endif
                            @if(Auth::user()->puedeEditarUsuarios())
                                <span class="badge bg-primary mb-1">Editar</span>
                            @endif
                            @if(Auth::user()->puedeEliminarUsuarios())
                                <span class="badge bg-danger mb-1">Eliminar</span>
                            @endif
                            @if(Auth::user()->puedeCambiarRoles())
                                <span class="badge bg-warning mb-1">Roles</span>
                            @endif
                            @if(Auth::user()->puedeActivarCuentas())
                                <span class="badge bg-info mb-1">Estado</span>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Header con botón crear condicional -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <div>
                        <h2 class="h4">
                            @if(Auth::user()->isAdmin())
                                <i class="fas fa-users-cog me-2"></i>Gestión Completa de Usuarios
                            @else
                                <i class="fas fa-users me-2"></i>Lista de Usuarios
                            @endif
                        </h2>
                        <p class="text-muted mb-0">
                            @if(Auth::user()->isAdmin())
                                Control total sobre usuarios y permisos del sistema
                            @else
                                Vista de usuarios según tus permisos asignados
                            @endif
                        </p>
                    </div>
                    
                    @if(Auth::user()->puedeCrearUsuarios())
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="fas fa-user-plus me-1"></i>Crear Nuevo Usuario
                        </button>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Información de permisos para usuarios no admin -->
                @if(!Auth::user()->isAdmin())
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Modo de Vista Limitada</h6>
                    <p class="mb-0">Solo puedes realizar acciones para las que tienes permisos específicos. Contacta al administrador si necesitas más acceso.</p>
                </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Lista de Usuarios Registrados
                            <span class="badge bg-secondary ms-2">{{ $usuarios->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Departamento</th>
                                        <th>Puesto</th>
                                        <th>Email</th>
                                        <th>Usuario</th>
                                        <th>Rol</th>
                                        <th>Permisos</th>
                                        <th>Estado</th>
                                        <th class="table-actions">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario->id_usuario }}</td>
                                        <td>
                                            <strong>{{ $usuario->nombre }}</strong>
                                            @if($usuario->id_usuario == Auth::user()->id_usuario)
                                                <span class="badge bg-info ms-1">Tú</span>
                                            @endif
                                        </td>
                                        <td>{{ $usuario->departamento }}</td>
                                        <td>{{ $usuario->puesto }}</td>
                                        <td>{{ $usuario->email }}</td>
                                        <td>
                                            @if($usuario->cuenta)
                                                <span class="badge bg-primary">{{ $usuario->cuenta->username }}</span>
                                            @else
                                                <span class="badge bg-secondary">Sin cuenta</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($usuario->cuenta)
                                                @if(Auth::user()->puedeCambiarRoles())
                                                    <form action="{{ route('admin.users.update-role', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <select name="rol" class="form-select form-select-sm" onchange="this.form.submit()">
                                                            <option value="1" {{ $usuario->cuenta->id_rol == 1 ? 'selected' : '' }}>Administrador</option>
                                                            <option value="2" {{ $usuario->cuenta->id_rol == 2 ? 'selected' : '' }}>Usuario</option>
                                                        </select>
                                                    </form>
                                                @else
                                                    <span class="badge {{ $usuario->cuenta->id_rol == 1 ? 'bg-danger' : 'bg-primary' }}">
                                                        {{ $usuario->cuenta->id_rol == 1 ? 'Admin' : 'Usuario' }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-warning">Sin rol</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($usuario->cuenta && Auth::user()->puedeCambiarRoles())
                                                <!-- Botón para abrir modal de permisos -->
                                                <button class="btn btn-outline-info btn-sm btn-permisos" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#permisosModal{{ $usuario->id_usuario }}">
                                                    <i class="fas fa-cog me-1"></i>Permisos
                                                </button>
                                            @elseif($usuario->cuenta)
                                                <!-- Visualización de permisos actuales (solo lectura) -->
                                                <small>
                                                    @foreach($usuario->cuenta->permisosNombres() as $permiso)
                                                        <span class="badge bg-info permisos-badge d-block mb-1">{{ $permiso }}</span>
                                                    @endforeach
                                                </small>
                                            @else
                                                <span class="badge bg-warning">Sin cuenta</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($usuario->cuenta)
                                                @if(Auth::user()->puedeActivarCuentas())
                                                    <form action="{{ route('admin.users.toggle-status', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        @if($usuario->cuenta->estado == 'activo')
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-check me-1"></i>Activo
                                                            </button>
                                                        @else
                                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                                <i class="fas fa-times me-1"></i>Inactivo
                                                            </button>
                                                        @endif
                                                    </form>
                                                @else
                                                    <span class="badge {{ $usuario->cuenta->estado == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $usuario->cuenta->estado == 'activo' ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-danger">Sin cuenta</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($usuario->cuenta)
                                                    <!-- Editar usuario -->
                                                    @if(Auth::user()->puedeEditarUsuarios())
                                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $usuario->id_usuario }}">
                                                            <i class="fas fa-edit me-1"></i>Editar
                                                        </button>
                                                    @endif
                                                    
                                                    <!-- Eliminar usuario -->
                                                    @if(Auth::user()->puedeEliminarUsuarios() && $usuario->id_usuario != Auth::user()->id_usuario)
                                                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $usuario->id_usuario }}">
                                                            <i class="fas fa-trash me-1"></i>Eliminar
                                                        </button>
                                                    @endif
                                                    
                                                    <!-- Gestionar permisos -->
                                                    @if(Auth::user()->puedeCambiarRoles())
                                                        <button class="btn btn-outline-info btn-sm btn-permisos" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#permisosModal{{ $usuario->id_usuario }}">
                                                            <i class="fas fa-cog me-1"></i>Permisos
                                                        </button>
                                                    @endif
                                                @else
                                                    <!-- Crear cuenta -->
                                                    @if(Auth::user()->puedeCrearUsuarios())
                                                        <form action="{{ route('admin.users.create-account', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-user-plus me-1"></i>Crear Cuenta
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    <!-- Eliminar usuario sin cuenta -->
                                                    @if(Auth::user()->puedeEliminarUsuarios())
                                                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $usuario->id_usuario }}">
                                                            <i class="fas fa-trash me-1"></i>Eliminar
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal de Permisos Flotante -->
                                    @if($usuario->cuenta && Auth::user()->puedeCambiarRoles())
                                    <div class="modal fade permisos-modal" id="permisosModal{{ $usuario->id_usuario }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.users.update-permissions', $usuario->id_usuario) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h6 class="modal-title">
                                                            <i class="fas fa-cog me-2"></i>Permisos: {{ $usuario->nombre }}
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @php
                                                            $permisosActuales = $usuario->cuenta->permisosArray() ?? [];
                                                        @endphp
                                                        
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permisos[]" value="1" 
                                                                   id="perm1{{ $usuario->id_usuario }}" 
                                                                   {{ in_array(1, $permisosActuales) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm1{{ $usuario->id_usuario }}">Ver Usuarios</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permisos[]" value="2" 
                                                                   id="perm2{{ $usuario->id_usuario }}"
                                                                   {{ in_array(2, $permisosActuales) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm2{{ $usuario->id_usuario }}">Gestionar Formatos</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permisos[]" value="3" 
                                                                   id="perm3{{ $usuario->id_usuario }}"
                                                                   {{ in_array(3, $permisosActuales) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm3{{ $usuario->id_usuario }}">Crear Usuarios</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permisos[]" value="4" 
                                                                   id="perm4{{ $usuario->id_usuario }}"
                                                                   {{ in_array(4, $permisosActuales) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm4{{ $usuario->id_usuario }}">Editar Usuarios</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permisos[]" value="5" 
                                                                   id="perm5{{ $usuario->id_usuario }}"
                                                                   {{ in_array(5, $permisosActuales) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm5{{ $usuario->id_usuario }}">Eliminar Usuarios</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permisos[]" value="6" 
                                                                   id="perm6{{ $usuario->id_usuario }}"
                                                                   {{ in_array(6, $permisosActuales) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm6{{ $usuario->id_usuario }}">Cambiar Roles</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permisos[]" value="7" 
                                                                   id="perm7{{ $usuario->id_usuario }}"
                                                                   {{ in_array(7, $permisosActuales) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm7{{ $usuario->id_usuario }}">Activar Cuentas</label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary btn-sm">Guardar Permisos</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Modal Editar Usuario -->
                                    @if($usuario->cuenta && Auth::user()->puedeEditarUsuarios())
                                    <div class="modal fade" id="editUserModal{{ $usuario->id_usuario }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.users.update', $usuario->id_usuario) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-edit me-2"></i>Editar Usuario: {{ $usuario->nombre }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nombre</label>
                                                            <input type="text" class="form-control" name="nombre" value="{{ $usuario->nombre }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Departamento</label>
                                                            <input type="text" class="form-control" name="departamento" value="{{ $usuario->departamento }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Puesto</label>
                                                            <input type="text" class="form-control" name="puesto" value="{{ $usuario->puesto }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Email</label>
                                                            <input type="email" class="form-control" name="email" value="{{ $usuario->email }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Usuario</label>
                                                            <input type="text" class="form-control" name="username" value="{{ $usuario->cuenta->username }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-save me-1"></i>Guardar Cambios
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Modal Eliminar Usuario -->
                                    @if(Auth::user()->puedeEliminarUsuarios() && $usuario->id_usuario != Auth::user()->id_usuario)
                                    <div class="modal fade" id="deleteUserModal{{ $usuario->id_usuario }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-danger">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Estás seguro de que quieres eliminar al usuario <strong>{{ $usuario->nombre }}</strong>?</p>
                                                    <p class="text-danger"><strong>¡Esta acción no se puede deshacer!</strong></p>
                                                    @if($usuario->cuenta)
                                                        <p>Se eliminará tanto el usuario como su cuenta.</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('admin.users.destroy', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash me-1"></i>Sí, Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para Crear Usuario -->
    @if(Auth::user()->puedeCrearUsuarios())
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus me-2"></i>Crear Nuevo Usuario
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Departamento</label>
                            <input type="text" class="form-control" name="departamento">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Puesto</label>
                            <input type="text" class="form-control" name="puesto">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Extensión</label>
                            <input type="text" class="form-control" name="extension">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre de Usuario *</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rol *</label>
                            <select name="rol" class="form-select" required>
                                <option value="2">Usuario Normal</option>
                                <option value="1">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Recargar la página cuando se envíen formularios
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    setTimeout(() => {
                        location.reload();
                    }, 100);
                });
            });
        });
    </script>
</body>
</html>