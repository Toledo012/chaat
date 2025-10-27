<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sistema de Formatos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
</head>
<body>
    <nav class="sidebar" id="navigation">
        <div class="logo">
            <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo del Sistema" class="logo">
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users-cog"></i> <span>Gestión de Usuarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.formatos.index') }}">
                    <i class="fas fa-file-alt"></i> <span>Gestión de Formatos</span>
                </a>
            </li>    
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.movimientos.index') }}">
                    <i class="fas fa-clipboard-list"></i> <span>Movimientos</span>
                </a>
            </li>
        </ul>
    </nav>

    <header class="admin-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button id="sidebarToggle" class="btn btn-sm text-white me-3" title="Contraer/Expandir">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <div>
                <h1>Panel de Administración</h1>
                <p class="subtitle mb-0">Control total del Sistema de Formatos Digitales</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
             <button id="darkModeToggle" class="btn btn-sm btn-outline-light" title="Alternar Modo Oscuro">
                <i class="fas fa-moon"></i> 
            </button>
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

    <main>
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

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
    </main> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('navigation');
            const toggleButton = document.getElementById('sidebarToggle');
            const darkModeToggle = document.getElementById('darkModeToggle');
            const body = document.body;
            
            // --- 1. Inicialización y Carga de Estados (Modo Oscuro y Sidebar) ---
            
            // Cargar estado de Modo Oscuro desde localStorage
            if (localStorage.getItem('darkModeEnabled') === 'true') {
                body.classList.add('dark-mode');
                darkModeToggle.querySelector('i').className = 'fas fa-sun'; 
            }

            // Cargar estado de la barra lateral desde localStorage
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }

            // --- 2. Funcionalidad de Toggle ---

            // Toggle de la Barra Lateral
            if (toggleButton && sidebar) {
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    
                    // Guardar el estado en localStorage
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                });
            }
            
            // Toggle del Modo Oscuro
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    body.classList.toggle('dark-mode');
                    
                    const isDarkMode = body.classList.contains('dark-mode');
                    localStorage.setItem('darkModeEnabled', isDarkMode);
                    
                    // Cambiar icono
                    darkModeToggle.querySelector('i').className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
                });
            }

            // --- 3. Animación de Carga (fadeInUp) ---
            
            const elements = document.querySelectorAll('.stats-card, .quick-actions .btn');
            elements.forEach((element, index) => {
                element.classList.add('loading');
                setTimeout(() => {
                    element.classList.add('loaded');
                }, index * 100);
            });

            // --- 4. Sidebar Toggle para móviles ---
            
            const mobileToggle = document.querySelector('[data-toggle="offcanvas"]');
            if(mobileToggle && sidebar){
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }

            // --- 5. Efecto hover mejorado ---
            const cards = document.querySelectorAll('.stats-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>