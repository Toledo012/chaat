<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Gestión de Usuarios - Sistema de Formatos</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ====== VARIABLES Y GENERAL ====== */
        :root {
            --primary-color: #017243d3;
            --secondary-color: #cf6290;
            --accent-color: #961010;
            --dark-color: #0e1a35;
            --light-color: #f8f9fa;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            color: var(--dark-color);
            min-height: 100vh;
        }

        a, a:hover, a:focus {
            text-decoration: none;
            outline: none;
        }

        /* ====== SIDEBAR MEJORADO ====== */
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, #015a35 100%);
            color: white;
            min-height: 100vh;
            padding: 1.5rem 0;
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            transition: all 0.3s ease;
            box-shadow: 3px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
            border-radius: 0 0px 25px 0;
            border-right: 4px solid var(--secondary-color);
        }

        .sidebar.hidden-xs {
            transform: translateX(-260px);
        }

        .sidebar .logo {
            text-align: center;
            margin-bottom: 2rem;
            padding: 0 1.5rem;
        }

        .sidebar .logo img {
            max-width: 160px;
            display: block;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: all 0.4s ease;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .sidebar .logo img:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
            border-color: rgba(255, 255, 255, 0.6);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 12px 20px;
            margin: 4px 15px;
            display: flex;
            align-items: center;
            border-radius: 12px;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }

        .sidebar .nav-link:hover::before {
            left: 100%;
        }

        .sidebar .nav-link i {
            color: var(--secondary-color);
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--secondary-color), #b84a7a);
            border-left: 4px solid var(--accent-color);
            color: white;
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .sidebar .nav-link:hover i,
        .sidebar .nav-link.active i {
            color: white;
            transform: scale(1.1);
        }

        /* ====== HEADER MEJORADO ====== */
        .admin-header {
            background: linear-gradient(135deg, var(--secondary-color), #b84a7a);
            color: white;
            padding: 1.5rem 2rem;
            margin-left: 260px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            border-radius: 0 0 25px 25px;
            border-bottom: 3px solid var(--primary-color);
        }

        .admin-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 60px;
            width: 120px;
            height: 100%;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.1));
            z-index: 1;
        }

        .admin-header h1 {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.25rem;
            position: relative;
            z-index: 2;
        }

        .admin-header .subtitle {
            font-weight: 400;
            opacity: 0.9;
            font-size: 0.95rem;
            position: relative;
            z-index: 2;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.15);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 10;
        }

        .admin-header .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            z-index: 20;
            cursor: pointer;
        }

        .admin-header .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        /* ====== CONTENIDO PRINCIPAL ====== */
        main {
            margin-left: 260px;
            transition: all 0.4s ease;
            padding: 2rem;
            background: transparent;
        }

        /* ====== TARJETAS Y TABLAS MEJORADAS ====== */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            overflow: hidden;
            position: relative;
            background: white;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .table-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(135deg, var(--primary-color), #015a35);
            color: white;
        }

        .table thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(1, 114, 67, 0.05);
            transform: translateX(5px);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #f1f3f4;
        }

        /* ====== BOTONES MEJORADOS ====== */
        .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }

        .btn-primary { 
            background: linear-gradient(135deg, #007bff, #0056b3); 
        }
        .btn-success { 
            background: linear-gradient(135deg, var(--success-color), #1e7e34); 
        }
        .btn-info { 
            background: linear-gradient(135deg, var(--info-color), #138496); 
        }
        .btn-warning { 
            background: linear-gradient(135deg, var(--warning-color), #e0a800); 
            color: #212529; 
        }
        .btn-danger { 
            background: linear-gradient(135deg, var(--danger-color), #c82333); 
        }
        .btn-secondary { 
            background: linear-gradient(135deg, #6c757d, #545b62); 
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* ====== BADGES MEJORADOS ====== */
        .badge {
            font-weight: 500;
            border-radius: 8px;
        }

        .permisos-badge {
            font-size: 0.7rem;
            margin: 1px;
        }

        /* ====== MODALES MEJORADOS ====== */
        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), #015a35);
            color: white;
            border-radius: 16px 16px 0 0;
            border: none;
        }

        .modal-title {
            font-weight: 600;
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

        /* ====== ALERTAS MEJORADAS ====== */
        .alert {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-left: 4px  solid transparent;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-left-color: var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border-left-color: var(--danger-color);
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            border-left-color: var(--info-color);
        }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-260px);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .admin-header, main {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .admin-header {
                padding: 1rem;
            }
            .admin-header h1 {
                font-size: 1.4rem;
            }
            .user-info {
                font-size: 0.9rem;
            }
            main {
                padding: 1rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
        }

        /* ====== ANIMACIONES ====== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card, .alert {
            animation: fadeInUp 0.6s ease-out;
        }

        /* --- Arreglo visual y z-index del backdrop para evitar bloqueo --- */
        .modal-backdrop.show {
            opacity: 0.35 !important;
            z-index: 1040 !important;
        }
        .modal {
            z-index: 1050 !important;
        }
        .modal.fade { display: none !important; }
        .modal.show { display: block !important; }
    </style>
</head>
<body>
    <!-- Sidebar Admin -->
    <nav class="sidebar" id="navigation">
        <div class="logo">
            <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo del Sistema" class="logo">
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users-cog"></i> Gestión de Usuarios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-file-alt"></i> Gestión de Formatos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-bar"></i> Reportes Avanzados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-cogs"></i> Configuración Sistema
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-database"></i> Respaldo de Datos
                </a>
            </li>
        </ul>
    </nav>

    <!-- Header Admin -->
    <header class="admin-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Gestión de Usuarios</h1>
            <p class="subtitle mb-0">Control total de usuarios y permisos del sistema</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="user-info">
                <span>Hola, <strong>{{ Auth::user()->usuario->nombre }}</strong></span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main>
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

        @if(!Auth::user()->isAdmin())
        <div class="alert alert-info">
            <h6><i class="fas fa-info-circle me-2"></i>Modo de Vista Limitada</h6>
            <p class="mb-0">Solo puedes realizar acciones para las que tienes permisos específicos. Contacta al administrador si necesitas más acceso.</p>
        </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 mb-1">
                    <i class="fas fa-users-cog me-2"></i>Gestión Completa de Usuarios
                </h2>
                <p class="text-muted mb-0">Control total sobre usuarios y permisos del sistema</p>
            </div>
            
            @if(Auth::user()->puedeCrearUsuarios())
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-user-plus me-1"></i>Crear Nuevo Usuario
                </button>
            @endif
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Lista de Usuarios Registrados
                    <span class="badge bg-secondary ms-2">{{ $usuarios->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
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
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                <tr
                                  data-id="{{ $usuario->id_usuario }}"
                                  data-nombre="{{ $usuario->nombre }}"
                                  data-departamento="{{ $usuario->departamento }}"
                                  data-puesto="{{ $usuario->puesto }}"
                                  data-email="{{ $usuario->email }}"
                                  data-username="{{ optional($usuario->cuenta)->username }}"
                                  data-has-account="{{ $usuario->cuenta ? '1' : '0' }}"
                                  data-permisos='@json($usuario->cuenta->permisosArray() ?? [])'
                                >
                                    <td><strong>{{ $usuario->id_usuario }}</strong></td>
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
                                            <button type="button" class="btn btn-outline-info btn-sm btn-permisos">
                                                <i class="fas fa-cog me-1"></i>Permisos
                                            </button>
                                        @elseif($usuario->cuenta)
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
                                                <!-- Mantengo el formulario inline para activar/desactivar (no genera modal) -->
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
                                                @if(Auth::user()->puedeEditarUsuarios())
                                                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit">
                                                        <i class="fas fa-edit me-1"></i>Editar
                                                    </button>
                                                @endif

                                                @if(Auth::user()->puedeEliminarUsuarios() && $usuario->id_usuario != Auth::user()->id_usuario)
                                                    <button type="button" class="btn btn-outline-danger btn-sm btn-delete">
                                                        <i class="fas fa-trash me-1"></i>Eliminar
                                                    </button>
                                                @endif
                                            @else
                                                @if(Auth::user()->puedeCrearUsuarios())
                                                    <form action="{{ route('admin.users.create-account', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-user-plus me-1"></i>Crear Cuenta
                                                        </button>
                                                    </form>
                                                @endif

                                                @if(Auth::user()->puedeEliminarUsuarios())
                                                    <button type="button" class="btn btn-outline-danger btn-sm btn-delete">
                                                        <i class="fas fa-trash me-1"></i>Eliminar
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- MODAL GLOBAL PARA EDITAR -->
    <div class="modal fade" id="globalEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="editModalContent"></div>
        </div>
    </div>

    <!-- MODAL GLOBAL PARA PERMISOS -->
    <div class="modal fade" id="globalPermisosModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="permisosModalContent"></div>
        </div>
    </div>

    <!-- MODAL GLOBAL PARA ELIMINAR -->
    <div class="modal fade" id="globalDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" id="deleteModalContent"></div>
        </div>
    </div>

    <!-- Modal para Crear Usuario (igual al original) -->
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

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- base URL para construir rutas dinámicas (ajusta si tu ruta es diferente) ---
            const baseUrl = '{{ url("admin/users") }}';

            // Limpieza del backdrop si queda colgado
            document.addEventListener('hidden.bs.modal', function () {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
            });

            // Reload suave tras envíos (mantengo tu lógica original)
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    setTimeout(() => { location.reload(); }, 120);
                });
            });

            // Helper: buscar datos de la fila por id
            function filaPorId(id) {
                return document.querySelector('tr[data-id="' + id + '"]');
            }

            // --- Editar ---
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // tomo el id desde la fila padre
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;
                    const nombre = tr.dataset.nombre || '';
                    const departamento = tr.dataset.departamento || '';
                    const puesto = tr.dataset.puesto || '';
                    const email = tr.dataset.email || '';
                    const username = tr.dataset.username || '';

                    const contenido = `
                        <form action="${baseUrl}/${id}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Usuario: ${nombre}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" name="nombre" value="${nombre.replaceAll('"','&quot;')}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Departamento</label>
                                    <input type="text" class="form-control" name="departamento" value="${departamento.replaceAll('"','&quot;')}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Puesto</label>
                                    <input type="text" class="form-control" name="puesto" value="${puesto.replaceAll('"','&quot;')}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="${email.replaceAll('"','&quot;')}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Usuario</label>
                                    <input type="text" class="form-control" name="username" value="${username ? username.replaceAll('"','&quot;') : ''}" ${ username ? 'required' : '' }>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    `;
                    document.getElementById('editModalContent').innerHTML = contenido;
                    new bootstrap.Modal(document.getElementById('globalEditModal')).show();
                });
            });

            // --- Permisos ---
            document.querySelectorAll('.btn-permisos').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;
                    const nombre = tr.dataset.nombre || '';
                    // permisos viene como JSON en data-permisos
                    let permisos = [];
                    try { permisos = JSON.parse(tr.dataset.permisos || '[]'); } catch(e) { permisos = []; }

                    const checked = (val) => permisos.includes(val) ? 'checked' : '';

                    const contenido = `
                        <form action="${baseUrl}/${id}/update-permissions" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h6 class="modal-title"><i class="fas fa-cog me-2"></i>Permisos: ${nombre}</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permisos[]" value="1" id="perm1" ${checked(1)}>
                                    <label class="form-check-label" for="perm1">Ver Usuarios</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permisos[]" value="2" id="perm2" ${checked(2)}>
                                    <label class="form-check-label" for="perm2">Gestionar Formatos</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permisos[]" value="3" id="perm3" ${checked(3)}>
                                    <label class="form-check-label" for="perm3">Crear Usuarios</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permisos[]" value="4" id="perm4" ${checked(4)}>
                                    <label class="form-check-label" for="perm4">Editar Usuarios</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permisos[]" value="5" id="perm5" ${checked(5)}>
                                    <label class="form-check-label" for="perm5">Eliminar Usuarios</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permisos[]" value="6" id="perm6" ${checked(6)}>
                                    <label class="form-check-label" for="perm6">Cambiar Roles</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permisos[]" value="7" id="perm7" ${checked(7)}>
                                    <label class="form-check-label" for="perm7">Activar Cuentas</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary btn-sm">Guardar Permisos</button>
                            </div>
                        </form>
                    `;
                    document.getElementById('permisosModalContent').innerHTML = contenido;
                    new bootstrap.Modal(document.getElementById('globalPermisosModal')).show();
                });
            });

            // --- Eliminar (confirmación) ---
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;
                    const nombre = tr.dataset.nombre || '';
                    const hasAccount = tr.dataset.hasAccount === '1';

                    const mensajeCuenta = hasAccount ? '<p>Se eliminará tanto el usuario como su cuenta.</p>' : '';

                    const contenido = `
                        <div class="modal-header">
                            <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>¿Estás seguro de que quieres eliminar al usuario <strong>${nombre}</strong>?</p>
                            <p class="text-danger"><strong>¡Esta acción no se puede deshacer!</strong></p>
                            ${mensajeCuenta}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form action="${baseUrl}/${id}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i>Sí, Eliminar
                                </button>
                            </form>
                        </div>
                    `;
                    document.getElementById('deleteModalContent').innerHTML = contenido;
                    new bootstrap.Modal(document.getElementById('globalDeleteModal')).show();
                });
            });

            // Abrir automáticamente el modal de permisos si viene desde ruta GET
            @if (session('open_permissions_id'))
                (function(){
                    const id = @json(session('open_permissions_id'));
                    const btn = document.querySelector(`tr[data-id="${id}"] .btn-permisos`);
                    if (btn) { btn.click(); }
                })();
            @endif

            // Evitar que un modal persistente deje backdrop bloqueando si hay errores
            // (ya añadimos cleanup en hidden.bs.modal, aquí limpiamos a petición)
            window.addEventListener('beforeunload', function() {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            });
        });
    </script>
</body>
</html>
