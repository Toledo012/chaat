<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    {{-- Enlaces CSS de Bootstrap, FontAwesome y Google Fonts --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- CSS PRINCIPAL --}}
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    
    @yield('styles')
</head>
<body>

    {{-- ===== SIDEBAR ===== --}}
    <nav class="sidebar" id="navigation">
        <div class="logo">
            <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo del Sistema" class="logo">
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('admin.users.*')) active @endif" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users-cog"></i> <span>Gestión de Usuarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('admin.formatos.*')) active @endif" href="{{ route('admin.formatos.index') }}">
                    <i class="fas fa-file-alt"></i> <span>Gestión de Formatos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('admin.movimientos.*')) active @endif" href="{{ route('admin.movimientos.index') }}">
                    <i class="fas fa-clipboard-list"></i> <span>Movimientos</span>
                </a>
            </li>
        </ul>
    </nav>
    
    {{-- ===== HEADER ===== --}}
    <header class="admin-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button id="sidebarToggle" class="btn btn-sm text-white me-3" title="Contraer/Expandir">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <div>
                <h1>@yield('header_title', 'Panel de Administración')</h1>
                <p class="subtitle mb-0">@yield('header_subtitle', 'Control total del Sistema de Formatos Digitales')</p>
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

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    <main>
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </main>

    {{-- ===== SCRIPTS ===== --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('navigation');
            const toggleButton = document.getElementById('sidebarToggle');
            const darkModeToggle = document.getElementById('darkModeToggle');
            const body = document.body;

            // === 1. Inicialización ===

            // Recuperar modo oscuro guardado
            const isDarkMode = localStorage.getItem('darkModeEnabled') === 'true';
            if (isDarkMode) {
                body.classList.add('dark-mode');
                if (darkModeToggle) {
                    darkModeToggle.querySelector('i').className = 'fas fa-sun';
                }
            }

            // Recuperar estado del sidebar
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed && sidebar) {
                sidebar.classList.add('collapsed');
            }

            // === 2. Funcionalidades ===

            // Alternar sidebar
            if (toggleButton && sidebar) {
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
            }

            // Alternar modo oscuro
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    body.classList.toggle('dark-mode');
                    const isDarkModeNow = body.classList.contains('dark-mode');
                    localStorage.setItem('darkModeEnabled', isDarkModeNow);
                    darkModeToggle.querySelector('i').className = isDarkModeNow ? 'fas fa-sun' : 'fas fa-moon';
                });
            }
        });
    </script>

    {{--Scripts adicionales de cada vista se cargan fuera del DOMContentLoaded --}}
    @yield('scripts')

</body>
</html>
