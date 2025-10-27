<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📋 Gestión de Formatos | SEMAHN</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('css/admin_dashboard.css') }}" rel="stylesheet">
  <style>
    .content-wrapper {
      margin-left: 260px;
      padding: 2rem;
      transition: margin-left 0.3s ease;
    }
    .sidebar.collapsed + .content-wrapper {
      margin-left: 80px;
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

  {{-- ===== HEADER SUPERIOR ===== --}}
  <header class="admin-header d-flex justify-content-between align-items-center px-4">
    

   <div class="d-flex align-items-center gap-3">
      <button id="toggleSidebar" class="btn btn-outline-light btn-sm">
        <i class="fas fa-bars"></i>

        
      </button>
        <div>
      

      <h1 class="h4 mb-0">Gestión de Formatos</h1>
      <span class="subtitle">Consulta, crea y exporta los formatos institucionales</span>
      <i class="fas fa-file-alt"></i>

    </div>

  </div>
  
    <div class="d-flex align-items-center gap-3">
      <button id="toggleTheme" class="theme-toggle" title="Alternar Modo Oscuro">
        <i class="fas fa-moon"></i>
      </button>

      <div class="user-info">{{ Auth::user()->name ?? 'Administrador' }}</div>
    </div>
    </div>  



  </header>

  {{-- ===== CONTENIDO PRINCIPAL ===== --}}
  <div class="content-wrapper">
    <div class="card shadow border-0">

      <div class="card-header bg-light text-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Formatos registrados</h5>

        
        <a href="{{ route('admin.formatos.create') }}" class="btn btn-light btn-sm">
          <i class="fas fa-plus me-1"></i>Nuevo Formato
        </a>
      </div>

      <div class="card-body">
        {{-- FILTROS --}}
        <form class="row g-3 mb-4" method="GET" action="{{ route('admin.formatos.index') }}">
          <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select">
              <option value="">Todos</option>
              <option value="A" {{ request('tipo')=='A' ? 'selected' : '' }}>A - Soporte</option>
              <option value="B" {{ request('tipo')=='B' ? 'selected' : '' }}>B - Equipos</option>
              <option value="C" {{ request('tipo')=='C' ? 'selected' : '' }}>C - Redes</option>
              <option value="D" {{ request('tipo')=='D' ? 'selected' : '' }}>D - Mantenimiento</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Usuario</label>
            <input type="text" name="usuario" class="form-control" value="{{ request('usuario') }}" placeholder="Buscar por usuario">
          </div>
          <div class="col-md-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-success w-100"><i class="fas fa-search me-1"></i>Filtrar</button>
          </div>
        </form>

        {{-- TABLA --}}
        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th>Tipo</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Vista previa</th>
                <th>PDF</th>
              </tr>
            </thead>
            <tbody>
              @forelse($formatos as $formato)
                <tr>
                  <td><strong>{{ $formato->tipo }}</strong></td>
                  <td>{{ $formato->nombre ?? 'Sin usuario' }}</td>
                  <td>{{ \Carbon\Carbon::parse($formato->fecha)->format('d/m/Y') }}</td>
                  <td>
                    @switch($formato->tipo)
                      @case('A') <a href="{{ route('admin.formatos.a.preview',$formato->id_servicio) }}" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i></a> @break
                      @case('B') <a href="{{ route('admin.formatos.b.preview',$formato->id_servicio) }}" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i></a> @break
                      @case('C') <a href="{{ route('admin.formatos.c.preview',$formato->id_servicio) }}" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i></a> @break
                      @case('D') <a href="{{ route('admin.formatos.d.preview',$formato->id_servicio) }}" class="btn btn-outline-info btn-sm"><i class="fas fa-eye"></i></a> @break
                      @default <span class="text-muted">-</span>
                    @endswitch
                  </td>
                  <td>
                    @switch($formato->tipo)
                      @case('A') <a href="{{ route('admin.formatos.a.pdf',$formato->id_servicio) }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a> @break
                      @case('B') <a href="{{ route('admin.formatos.b.pdf',$formato->id_servicio) }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a> @break
                      @case('C') <a href="{{ route('admin.formatos.c.pdf',$formato->id_servicio) }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a> @break
                      @case('D') <a href="{{ route('admin.formatos.d.pdf',$formato->id_servicio) }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a> @break
                      @default <span class="text-muted">-</span>
                    @endswitch
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted">No hay formatos registrados</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="text-end mt-3">
          <a href="{{ route('admin.formatos.reporte.general') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-chart-line me-1"></i>Reporte General
          </a>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Toggle sidebar
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.querySelector('.sidebar').classList.toggle('collapsed');
    });

    // Modo oscuro
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
