
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sistema de Formatos</title>

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
    
    /* Bordes redondeados solo en esquina superior derecha e inferior derecha */
    border-radius: 0 0px 25px 0;
    
    /* Borde decorativo en el lado derecho */
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
            color: black;
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
    
    /* Bordes redondeados solo en esquina inferior */
    border-radius: 0 0 25px 25px;
    
    /* Borde decorativo inferior */
    border-bottom: 3px solid var(--primary-color);
}

        .admin-header::before {
            content: '';
            position: absolute; 
            top: 0;
            right: 0;
            
            
            height: 100%;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.1));
        }

        .admin-header h1 {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.25rem;
        }

        .admin-header .subtitle {
            font-weight: 400;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.15);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .admin-header .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
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

        /* ====== TARJETAS DE ESTADÍSTICAS MEJORADAS ====== */
        .stats-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            overflow: hidden;
            position: relative;
            background: white;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .stats-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .stats-icon {
            font-size: 2.8rem;
            opacity: 0.9;
            transition: all 0.3s ease;
        }

        .stats-card:hover .stats-icon {
            transform: scale(1.1);
            opacity: 1;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0;
            background: linear-gradient(135deg, var(--dark-color), #2c3e50);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stats-label {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
        }

        /* ====== ACCIONES RÁPIDAS MEJORADAS ====== */
        .quick-actions .btn {
            border-radius: 12px;
            padding: 1rem 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 100px;
        }

        .quick-actions .btn i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .quick-actions .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        /* Colores específicos para botones */
        .btn-primary { background: linear-gradient(135deg, #007bff, #0056b3); }
        .btn-success { background: linear-gradient(135deg, var(--success-color), #1e7e34); }
        .btn-info { background: linear-gradient(135deg, var(--info-color), #138496); }
        .btn-warning { background: linear-gradient(135deg, var(--warning-color), #e0a800); color: #212529; }
        .btn-danger { background: linear-gradient(135deg, var(--danger-color), #c82333); }
        .btn-secondary { background: linear-gradient(135deg, #6c757d, #545b62); }

        /* ====== ALERTAS MEJORADAS ====== */
        .alert {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-left-color: var(--success-color);
        }

        /* ====== RESPONSIVE MEJORADO ====== */
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
            
            .quick-actions .btn {
                min-height: 80px;
                margin-bottom: 1rem;
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
        }

        /* ====== ANIMACIONES ADICIONALES ====== */
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

        .stats-card, .quick-actions .btn {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Efecto de carga suave */
        .loading {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }

        .loaded {
            opacity: 1;
            transform: translateY(0);
        }
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
                <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.users.index') }}">
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
            <h1>Panel de Administración</h1>
            <p class="subtitle mb-0">Control total del Sistema de Formatos Digitales</p>
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
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Estadísticas -->
        <div class="row mb-5">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">TOTAL USUARIOS</div>
                            <div class="stats-number">{{ $stats['total_usuarios'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-users stats-icon text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">CUENTAS ACTIVAS</div>
                            <div class="stats-number">{{ $stats['cuentas_activas'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-user-check stats-icon text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">TOTAL SERVICIOS</div>
                            <div class="stats-number">{{ $stats['total_servicios'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-clipboard-list stats-icon text-info"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-label">FORMATOS</div>
                            <div class="stats-number">{{ $stats['total_formatos'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-file-alt stats-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
<!-- 
{{--
        <div class="row mb-4">
            <div class="col-12">
                <div class="card p-4">
                    <h4 class="mb-4">Acciones Rápidas</h4>
                    <div class="row quick-actions g-3">
                        <div class="col-md-2 col-sm-4 col-6">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                                <i class="fas fa-users"></i>
                                <span>Gestión Usuarios</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6">
                            <a href="#" class="btn btn-success">
                                <i class="fas fa-user-plus"></i>
                                <span>Nuevo Usuario</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6">
                            <a href="#" class="btn btn-info">
                                <i class="fas fa-file-alt"></i>
                                <span>Nuevo Formato</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6">
                            <a href="#" class="btn btn-warning">
                                <i class="fas fa-chart-bar"></i>
                                <span>Ver Reportes</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6">
                            <a href="#" class="btn btn-danger">
                                <i class="fas fa-cogs"></i>
                                <span>Configuración</span>
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4 col-6">
                            <a href="#" class="btn btn-secondary">
                                <i class="fas fa-database"></i>
                                <span>Respaldo</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>  -->
--}}


    

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts Mejorados -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de carga
            const elements = document.querySelectorAll('.stats-card, .quick-actions .btn');
            elements.forEach((element, index) => {
                element.classList.add('loading');
                setTimeout(() => {
                    element.classList.add('loaded');
                }, index * 100);
            });

            // Sidebar toggle para móviles
            const toggle = document.querySelector('[data-toggle="offcanvas"]');
            const nav = document.getElementById('navigation');
            if(toggle && nav){
                toggle.addEventListener('click', function() {
                    nav.classList.toggle('show');
                });
            }

            // Efecto hover mejorado
            const cards = document.querySelectorAll('.stats-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>