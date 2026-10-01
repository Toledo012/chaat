<!DOCTYPE html>
<html lang="es">
<head>

<link rel="icon" href="{{ asset('favicon.ico') }}?v=1">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Departamento')</title>
    <script src="{{ asset('js/theme.js') }}"></script>

    {{-- CSS principal  --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">

    @yield('styles')
</head>
<body>

    {{-- ===== SIDEBAR (DEPARTAMENTO) ===== --}}
    <nav class="sidebar" id="navigation" aria-label="Navegación principal">
        <button type="button" id="sidebarClose" class="sidebar-close btn btn-sm btn-outline-secondary" aria-label="Cerrar menú"><i class="fas fa-xmark" aria-hidden="true"></i></button>
        <div class="logo">
            <a href="{{ route('departamento.dashboard') }}">
                <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo del Sistema" class="logo">
            </a>
        </div>

        <ul class="nav flex-column">

            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('departamento.dashboard')) active @endif"
                   href="{{ route('departamento.dashboard') }}">
                    <i class="fas fa-home"></i> <span>Mi Panel (Departamento)</span>
                </a>
            </li>

            {{-- Tickets (V2)--}}
            {{--  --}}
            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('departamento.tickets.*')) active @endif"
                   href="{{ route('departamento.tickets.index') }}">
                    <i class="fas fa-plus-circle"></i> <span>Crear Ticket</span>
                </a>
            </li>

            
            <li class="nav-item">
                <a class="nav-link @if(request()->routeIs('departamento.dashboard')) active @endif"
                   href="{{ route('departamento.dashboard') }}">
                    <i class="fas fa-ticket-alt"></i> <span>Mis Tickets</span>
                </a>
            </li>

        </ul>
    </nav>
    <div id="sidebarBackdrop" class="sidebar-backdrop" hidden></div>

    {{-- ===== HEADER  ===== --}}
    <header class="admin-header d-flex justify-content-between align-items-center">
        <div class="header-title-group d-flex align-items-center">
            <button type="button" id="sidebarToggle" class="btn btn-sm text-white" aria-controls="navigation" aria-expanded="true" aria-label="Contraer menú">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <div class="header-title-text">
                <h1>@yield('header_title', 'Panel de Departamento')</h1>
                <p class="subtitle mb-0">@yield('header_subtitle', 'Solicitudes de servicio y seguimiento')</p>
            </div>
            @include('partials.header-clock')
        </div>

        <div class="header-actions d-flex align-items-center gap-3">
            <button type="button" id="darkModeToggle" class="btn btn-sm btn-outline-light" aria-label="Activar modo oscuro" aria-pressed="false">
                <i class="fas fa-moon"></i>
            </button>

            <div class="user-info">
                <span>Hola, <strong>{{ Auth::user()->usuario->nombre ?? 'Usuario' }}</strong></span>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-triangle-exclamation me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ===== SCRIPTS (mismo que admin) ===== --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('js/navigation.js') }}"></script>
    <script src="{{ asset('js/header-clock.js') }}"></script>

    @yield('scripts')
</body>
</html>
