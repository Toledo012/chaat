<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formato A - Soporte / Desarrollo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('css/admin_dashboard.css') }}" rel="stylesheet">
  <style>
    .content-wrapper { margin-left: 260px; padding: 2rem; animation: fadeInUp 0.6s ease-out; }
    .card { border-radius: 10px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05); border: none; }
    .card-header { background-color: #399e91; color: white; font-weight: 600; }
    .form-control, .form-select { border-radius: 8px; }
    .btn-primary { background-color: #399e91; border-color: #399e91; }
    .btn-primary:hover { background-color: #2f847a; }
    .theme-toggle { border: none; background: transparent; color: white; cursor: pointer; }
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
      <li class="nav-item"><a class="nav-link active" href="{{ route('admin.formatos.index') }}"><i class="fas fa-file-alt"></i> <span>Gestión de Formatos</span></a></li>    
      <li class="nav-item"><a class="nav-link" href="{{ route('admin.movimientos.index') }}"><i class="fas fa-clipboard-list"></i> <span>Movimientos</span></a></li>
    </ul>
  </nav>

  {{-- ===== HEADER ===== --}}
  <header class="admin-header d-flex justify-content-between align-items-center px-4">
    <div>
      <h1 class="h5 mb-0">Formato A - Soporte / Desarrollo</h1>
      <span class="subtitle">Registro y documentación de actividades de soporte</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <button id="toggleSidebar" class="btn btn-outline-light btn-sm"><i class="fas fa-bars"></i></button>
      <button id="toggleTheme" class="theme-toggle"><i class="fas fa-moon"></i></button>
      <div class="user-info">{{ Auth::user()->name ?? 'Administrador' }}</div>
    </div>
  </header>

  {{-- ===== CONTENIDO ===== --}}
  <div class="content-wrapper">
    <div class="card">
      <div class="card-header"><i class="fas fa-laptop-code me-2"></i>Formulario de Registro</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.formatos.a.store') }}">
          @csrf

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">Tipo de Atención</label>
              <select name="tipo_atencion" class="form-select">
                <option>Memo</option><option>Teléfono</option><option>Jefe</option><option>Usuario</option>
              </select>
            </div>
            <div class="col-md-8">
              <label class="form-label">Petición</label>
              <textarea name="peticion" class="form-control" rows="2"></textarea>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Tipo de Servicio</label>
              <select name="tipo_servicio" class="form-select">
                <option>Equipos</option><option>Redes LAN/WAN</option><option>Antivirus</option><option>Software</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Trabajo Realizado</label>
              <select name="trabajo_realizado" class="form-select">
                <option>En sitio</option><option>Área de producción</option><option>Traslado de equipo</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Conclusión del Servicio</label>
            <select name="conclusion_servicio" class="form-select">
              <option>Terminado</option><option>En proceso</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Trabajo Específico Realizado</label>
            <textarea name="te_realizado" class="form-control" rows="3"></textarea>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <input name="firma_usuario" placeholder="Solicitante" class="form-control">
            </div>
            <div class="col-md-4">
              <input name="firma_tecnico" readonly value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}" class="form-control">
            </div>
            <div class="col-md-4">
              <input name="firma_jefe_area" readonly value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}" class="form-control">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="2"></textarea>
          </div>

          <div class="text-end">
            <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
            <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('toggleSidebar').addEventListener('click', () => {
      document.querySelector('.sidebar').classList.toggle('collapsed');
    });
    const toggleBtn = document.getElementById('toggleTheme');
    toggleBtn.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      toggleBtn.innerHTML = document.body.classList.contains('dark-mode') 
        ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    });
  </script>

</body>
</html>
