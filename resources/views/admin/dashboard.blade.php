<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sistema de Formatos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stats-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .quick-actions .btn {
            border-radius: 8px;
            padding: 12px 20px;
            font-weight: 500;
        }
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
    </style>
</head>
<body>
    <!-- Header Admin -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 mb-1">Panel de Administración</h1>
                    <p class="mb-0 opacity-75">Control total del Sistema de Formatos Digitales</p>
                </div>
                <div class="col-md-4 text-end">
                    <span class="me-3">Hola, <strong>{{ Auth::user()->usuario->nombre }}</strong></span>
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
            <!-- Sidebar Admin -->
            <nav class="col-md-3 col-lg-2 sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-warning">MENÚ ADMIN</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users-cog me-2"></i>Gestión de Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-file-alt me-2"></i>Gestión de Formatos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-chart-bar me-2"></i>Reportes Avanzados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cogs me-2"></i>Configuración Sistema
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-database me-2"></i>Respaldo de Datos
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card border-left-primary">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-primary fw-bold">TOTAL USUARIOS</div>
                                        <div class="h2 mb-0">{{ $stats['total_usuarios'] ?? 0 }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="fas fa-users stats-icon text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card border-left-success">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-success fw-bold">CUENTAS ACTIVAS</div>
                                        <div class="h2 mb-0">{{ $stats['cuentas_activas'] ?? 0 }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="fas fa-user-check stats-icon text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card border-left-info">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-info fw-bold">TOTAL SERVICIOS</div>
                                        <div class="h2 mb-0">{{ $stats['total_servicios'] ?? 0 }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="fas fa-clipboard-list stats-icon text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card border-left-warning">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-warning fw-bold">FORMATOS</div>
                                        <div class="h2 mb-0">{{ $stats['total_formatos'] ?? 0 }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="fas fa-file-alt stats-icon text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row quick-actions">
                                    <div class="col-xl-2 col-md-4 mb-3">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-100">
                                            <i class="fas fa-users me-2"></i>Gestión Usuarios
                                        </a>
                                    </div>
                                    <div class="col-xl-2 col-md-4 mb-3">
                                        <a href="#" class="btn btn-success w-100">
                                            <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                                        </a>
                                    </div>
                                    <div class="col-xl-2 col-md-4 mb-3">
                                        <a href="#" class="btn btn-info w-100">
                                            <i class="fas fa-file-alt me-2"></i>Nuevo Formato
                                        </a>
                                    </div>
                                    <div class="col-xl-2 col-md-4 mb-3">
                                        <a href="#" class="btn btn-warning w-100">
                                            <i class="fas fa-chart-bar me-2"></i>Ver Reportes
                                        </a>
                                    </div>
                                    <div class="col-xl-2 col-md-4 mb-3">
                                        <a href="#" class="btn btn-danger w-100">
                                            <i class="fas fa-cogs me-2"></i>Configuración
                                        </a>
                                    </div>
                                    <div class="col-xl-2 col-md-4 mb-3">
                                        <a href="#" class="btn btn-secondary w-100">
                                            <i class="fas fa-database me-2"></i>Respaldo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actividad Reciente -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history me-2"></i>Actividad Reciente del Sistema
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"></h6>
                                            <small class="text-muted"></small>
                                        </div>
                                        <p class="mb-1"></p>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"></h6>
                                            <small class="text-muted">10m</small>
                                        </div>
                                        <p class="mb-1">eddy mampo</p>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"></h6>
                                            <small class="text-muted"></small>
                                        </div>
                                        <p class="mb-1"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Información del Sistema
                                </h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Versión:</strong> 2.1.0</p>
                                <p><strong>Última actualización:</strong> 15/01/2024</p>
                                <p><strong>Usuarios en línea:</strong> 3</p>
                                <p><strong>Estado:</strong> <span class="badge bg-success">Operativo</span></p>
                                <hr>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Sistema seguro con control de acceso por permisos
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>