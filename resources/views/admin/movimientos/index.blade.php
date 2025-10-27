<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Movimientos (Auditoría del Sistema)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('css/admin_dashboard.css') }}" rel="stylesheet">
  <style>
    .content-wrapper { margin-left: 260px; padding: 2rem; animation: fadeInUp 0.6s ease-out; transition: margin-left 0.3s ease; }
    .sidebar.collapsed + .content-wrapper { margin-left: 80px; }
    .card-header { background-color: #399e91; color: white; font-weight: 600; }
    .theme-toggle { border: none; background: transparent; color: white; cursor: pointer; font-size: 1.2rem; transition: transform 0.3s ease; }
    .theme-toggle:hover { transform: scale(1.1); }
    pre { max-height: 200px; overflow-y: auto; background: #f8f9fa; border-radius: 8px; }
    .dark-mode pre { background: #2b3038; color: #e9ecef; }
  </style>
</head>
<body>

{{-- ===== SIDEBAR ===== --}}
<nav class="sidebar" id="navigation">
  <div class="logo">
    <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo del Sistema" class="logo">
  </div>
  <ul class="nav flex-column">
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}"><i class="fas fa-users-cog"></i> <span>Gestión de Usuarios</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.formatos.index') }}"><i class="fas fa-file-alt"></i> <span>Gestión de Formatos</span></a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.movimientos.index') }}"><i class="fas fa-clipboard-list"></i> <span>Movimientos</span></a></li>
  </ul>
</nav>

{{-- ===== HEADER ===== --}}
<header class="admin-header d-flex justify-content-between align-items-center px-4">
  <div>
    <h1 class="h5 mb-0">Auditoría de Movimientos</h1>
    <span class="subtitle">Registro detallado de cambios realizados en el sistema</span>
  </div>
  <div class="d-flex align-items-center gap-3">
    <button id="toggleSidebar" class="btn btn-outline-light btn-sm"><i class="fas fa-bars"></i></button>
    <button id="toggleTheme" class="theme-toggle"><i class="fas fa-moon"></i></button>
    <div class="user-info">{{ Auth::user()->name ?? 'Administrador' }}</div>
  </div>
</header>

{{-- ===== CONTENIDO ===== --}}
<div class="content-wrapper">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="fas fa-database me-2 text-success"></i>Registro de movimientos</h4>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Volver</a>
  </div>

  {{-- 🔍 FILTROS --}}
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-2">
      <input type="text" class="form-control" name="tabla" placeholder="Tabla" value="{{ request('tabla') }}">
    </div>
    <div class="col-md-2">
      <select class="form-select" name="accion">
        <option value="">Acción</option>
        @foreach(['INSERT','UPDATE','DELETE'] as $a)
          <option value="{{ $a }}" @selected(request('accion')===$a)>{{ $a }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <input type="text" class="form-control" name="usuario" placeholder="Usuario" value="{{ request('usuario') }}">
    </div>
    <div class="col-md-2">
      <input type="date" class="form-control" name="desde" value="{{ request('desde') }}">
    </div>
    <div class="col-md-2">
      <input type="date" class="form-control" name="hasta" value="{{ request('hasta') }}">
    </div>
    <div class="col-md-2 d-grid">
      <button class="btn btn-primary"><i class="fas fa-search me-1"></i>Filtrar</button>
    </div>
  </form>

  <div class="d-flex justify-content-end mb-3">
    <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.movimientos.index', array_merge(request()->query(), ['export' => 1, 'autoprint' => 0])) }}" target="_blank">
      <i class="fas fa-file-pdf me-1"></i> Exportar PDF
    </a>
  </div>

  {{-- 📋 TABLA --}}
  <div class="card shadow border-0">
    <div class="card-header"><i class="fas fa-list me-2"></i>Movimientos registrados</div>
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead class="table-success">
          <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Tabla</th>
            <th>Acción</th>
            <th>ID Registro</th>
            <th>Detalles</th>
          </tr>
        </thead>
        <tbody>
          @forelse($movimientos as $m)
          <tr>
            <td>{{ $m->fecha }}</td>
            <td>{{ $m->username ?? '—' }}</td>
            <td>{{ $m->tabla }}</td>
            <td>
              <span class="badge bg-{{ $m->accion==='DELETE'?'danger':($m->accion==='UPDATE'?'warning text-dark':'success') }}">
                {{ $m->accion }}
              </span>
            </td>
            <td>{{ $m->id_registro }}</td>
            <td>
              <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#det{{ $m->id_movimiento }}">
                <i class="fas fa-eye"></i> Ver
              </button>
            </td>
          </tr>
          <tr class="collapse" id="det{{ $m->id_movimiento }}">
            <td colspan="6">
              <div class="row">
                <div class="col-md-6">
                  <h6><i class="fas fa-arrow-left me-1 text-danger"></i>Antes</h6>
                  <pre class="small">{{ json_encode(json_decode($m->datos_anteriores ?? 'null', true), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
                <div class="col-md-6">
                  <h6><i class="fas fa-arrow-right me-1 text-success"></i>Después</h6>
                  <pre class="small">{{ json_encode(json_decode($m->datos_nuevos ?? 'null', true), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
              </div>
            </td>
          </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">Sin movimientos registrados</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer bg-light">{{ $movimientos->links() }}</div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('toggleSidebar').addEventListener('click',()=>document.querySelector('.sidebar').classList.toggle('collapsed'));
  document.getElementById('toggleTheme').addEventListener('click',()=>{
    document.body.classList.toggle('dark-mode');
    document.getElementById('toggleTheme').innerHTML = 
      document.body.classList.contains('dark-mode') 
      ? '<i class="fas fa-sun"></i>' 
      : '<i class="fas fa-moon"></i>';
  });
</script>
</body>
</html>
