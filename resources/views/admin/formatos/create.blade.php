<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>🧩 Crear Formato | SEMAHN</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('css/admin_dashboard.css') }}" rel="stylesheet">

  <style>
    .content-wrapper {
      margin-left: 260px;
      padding: 2rem;
      transition: margin-left 0.3s ease;
      animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(25px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card-option {
      border: 1px solid #dee2e6;
      border-radius: var(--border-radius);
      background: var(--card-bg);
      box-shadow: var(--shadow-light);
      padding: 1.5rem;
      transition: all 0.3s ease;
      cursor: pointer;
      text-align: center;
      animation: fadeInUp 0.8s ease both;
    }

    .card-option:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }

    .card-option i {
      font-size: 2.2rem;
      color: var(--primary-color);
      margin-bottom: 0.75rem;
    }

    .card-option h5 {
      font-weight: 700;
      color: var(--dark-color);
      margin-bottom: 0.3rem;
    }

    .card-option p {
      font-size: 0.9rem;
      color: var(--secondary-color);
      margin-bottom: 1rem;
    }

    .card-option .btn {
      border-radius: var(--border-radius);
      padding: 0.4rem 1rem;
    }

    .theme-toggle {
      border: none;
      background: transparent;
      color: white;
      font-size: 1.2rem;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .theme-toggle:hover { transform: scale(1.1); }

  </style>
</head>
<body>

  {{-- ===== SIDEBAR ===== --}}
  <nav class="sidebar" id="navigation">
    <div class="logo">
      <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo del Sistema" class="logo">
    </div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
          <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.users.index') }}">
          <i class="fas fa-users-cog"></i> <span>Gestión de Usuarios</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.formatos.index') }}">
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

  {{-- ===== HEADER ===== --}}
<header class="admin-header d-flex justify-content-between align-items-center px-4">
    
  
  <div class="d-flex align-items-center gap-3"> 
  <button id="toggleSidebar" class="btn btn-outline-light btn-sm">
        <i class="fas fa-bars"></i>
      </button>
    
  <div>
      
      <h1 class="h4 mb-0">Crear nuevo formato</h1>
      <span class="subtitle">Selecciona el tipo de formato a generar</span>
    </div>
  </div>

     
    <div class="d-flex align-items-center gap-3">
    
      <button id="toggleTheme" class="theme-toggle" title="Cambiar tema">
        <i class="fas fa-moon"></i>
      </button>

      <div class="user-info">{{ Auth::user()->name ?? 'Administrador' }}</div>
    </div>
  </header>

  {{-- ===== CONTENIDO PRINCIPAL ===== --}}
  <div class="content-wrapper container">
    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="card-option">
          <i class="fas fa-laptop-code"></i>
          <h5>Formato A</h5>
          <p>Soporte técnico o desarrollo de software.</p>
          <a href="{{ route('admin.formatos.a') }}" class="btn btn-primary btn-sm">Crear</a>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card-option">
          <i class="fas fa-desktop"></i>
          <h5>Formato B</h5>
          <p>Equipos de cómputo e impresoras.</p>
          <a href="{{ route('admin.formatos.b') }}" class="btn btn-primary btn-sm">Crear</a>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card-option">
          <i class="fas fa-network-wired"></i>
          <h5>Formato C</h5>
          <p>Servicios de redes y telefonía.</p>
          <a href="{{ route('admin.formatos.c') }}" class="btn btn-primary btn-sm">Crear</a>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="card-option">
          <i class="fas fa-tools"></i>
          <h5>Formato D</h5>
          <p>Mantenimiento a equipos personales.</p>
          <a href="{{ route('admin.formatos.d') }}" class="btn btn-primary btn-sm">Crear</a>
        </div>
      </div>
    </div>

    <div class="text-end mt-4">
      <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver al listado
      </a>
    </div>
  </div>

  {{-- ===== JS ===== --}}
  <script>
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.querySelector('.sidebar').classList.toggle('collapsed');
    });

    const toggleBtn = document.getElementById('toggleTheme');
    toggleBtn.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      toggleBtn.innerHTML = document.body.classList.contains('dark-mode')
        ? '<i class="fas fa-sun"></i>'
        : '<i class="fas fa-moon"></i>';
    });
  </script>

</body>
</html>
