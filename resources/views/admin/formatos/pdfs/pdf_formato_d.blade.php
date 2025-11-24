<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formato D - Mantenimiento Personal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('css/admin_dashboard.css') }}" rel="stylesheet">
  <style>
    .content-wrapper { margin-left: 260px; padding: 2rem; animation: fadeInUp 0.6s ease-out; }
    .card-header { background-color: #399e91; color: white; font-weight: 600; }
  </style>
</head>
<body>
  <nav class="sidebar" id="navigation">
    <div class="logo">
      <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo del Sistema">
    </div>
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li class="nav-item"><a class="nav-link active" href="{{ route('admin.formatos.index') }}"><i class="fas fa-tools"></i> Formatos</a></li>
    </ul>
  </nav>

  <header class="admin-header d-flex justify-content-between align-items-center px-4">
    <h1 class="h5 mb-0">Formato D - Mantenimiento Personal</h1>
    <div class="user-info">{{ Auth::user()->name ?? 'Administrador' }}</div>
  </header>

  <div class="content-wrapper">
    <div class="card shadow border-0">
      <div class="card-header"><i class="fas fa-tools me-2"></i>Registro de Entrega / Mantenimiento</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.formatos.d.store') }}">
          @csrf
          <div class="row mb-3">
            <div class="col-md-4"><label>Fecha</label><input type="date" name="fecha" class="form-control"></div>
            <div class="col-md-4"><label>Equipo</label><input name="equipo" class="form-control"></div>
            <div class="col-md-4"><label>Marca</label><select name="marca" class="form-select"><option>HP</option><option>Lenovo</option><option>Dell</option><option>Asus</option></select></div>
          </div>
          <div class="row mb-3">
            <div class="col-md-4"><label>Modelo</label><input name="modelo" class="form-control"></div>
            <div class="col-md-4"><label>Serie</label><input name="serie" class="form-control"></div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4"><input name="otorgante" placeholder="Otorgante" class="form-control"></div>
            <div class="col-md-4"><input name="receptor" readonly value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}" class="form-control"></div>
            <div class="col-md-4"><input name="firma_jefe_area" readonly value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}" class="form-control"></div>
          </div>

          <div class="mb-3"><label>Observaciones</label><textarea name="observaciones" class="form-control" rows="3"></textarea></div>
          <div class="text-end">
            <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
            <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
