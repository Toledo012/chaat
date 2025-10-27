<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formato C - Redes y Telefonía</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('css/admin_dashboard.css') }}" rel="stylesheet">
  <style>
    .content-wrapper { margin-left: 260px; padding: 2rem; animation: fadeInUp 0.6s ease-out; }
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
    <h1 class="h5 mb-0">Formato C - Redes y Telefonía</h1>
    <span class="subtitle">Registro de mantenimiento e instalación de redes</span>
  </div>
  <div class="d-flex align-items-center gap-3">
    <button id="toggleSidebar" class="btn btn-outline-light btn-sm"><i class="fas fa-bars"></i></button>
    <button id="toggleTheme" class="theme-toggle"><i class="fas fa-moon"></i></button>
    <div class="user-info">{{ Auth::user()->name ?? 'Administrador' }}</div>
  </div>
</header>

{{-- ===== CONTENIDO ===== --}}
<div class="content-wrapper">
  <div class="card shadow border-0">
    <div class="card-header"><i class="fas fa-network-wired me-2"></i>Formulario de Formato C</div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.formatos.c.store') }}">
        @csrf
        <div class="row mb-3">
          <div class="col-md-4">
            <label>Tipo de red</label>
            <select name="tipo_red" class="form-select" required>
              <option value="">Seleccionar</option>
              <option>Red</option><option>Telefonía</option>
            </select>
          </div>
          <div class="col-md-4">
            <label>Tipo de servicio</label>
            <select name="tipo_servicio" class="form-select" required>
              <option value="">Seleccionar</option>
              <option>Preventivo</option><option>Correctivo</option><option>Configuración</option>
            </select>
          </div>
          <div class="col-md-4">
            <label>Descripción</label>
            <input name="descripcion_servicio" class="form-control" placeholder="Ej. Instalación de cableado, revisión...">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label>Diagnóstico</label>
            <textarea name="diagnostico" class="form-control" rows="2" placeholder="Describe brevemente el problema"></textarea>
          </div>
          <div class="col-md-6">
            <label>Origen de la falla</label>
            <select name="origen_falla" class="form-select">
              <option>Desgaste natural</option><option>Mala operación</option><option>Otro</option>
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label>Trabajo realizado</label>
            <textarea name="trabajo_realizado" class="form-control" rows="2" placeholder="Ej. Reconfiguración, cambio de cableado..."></textarea>
          </div>
          <div class="col-md-6">
            <label>Detalle del servicio</label>
            <textarea name="detalle_realizado" class="form-control" rows="2" placeholder="Detalles adicionales del trabajo"></textarea>
          </div>
        </div>

        <hr>
        <h6><i class="fas fa-cogs me-1"></i>Materiales utilizados</h6>
        <table class="table table-bordered" id="tablaMateriales">
          <thead class="table-light"><tr><th>Material</th><th>Cantidad</th><th>Acción</th></tr></thead>
          <tbody>
            <tr>
              <td>
                <select name="materiales[0][id_material]" class="form-select">
                  <option value="">Seleccionar material</option>
                  @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                    <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                  @endforeach
                </select>
              </td>
              <td><input type="number" name="materiales[0][cantidad]" class="form-control" min="1"></td>
              <td class="text-center"><button type="button" class="btn btn-outline-success btn-sm" onclick="agregarFila()">+</button></td>
            </tr>
          </tbody>
        </table>

        <hr>
        <div class="row mb-3">
          <div class="col-md-4"><input name="firma_usuario" placeholder="Solicitante" class="form-control"></div>
          <div class="col-md-4"><input name="firma_tecnico" readonly value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}" class="form-control"></div>
          <div class="col-md-4"><input name="firma_jefe_area" readonly value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}" class="form-control"></div>
        </div>

        <div class="mb-3"><label>Observaciones</label><textarea name="observaciones" class="form-control" rows="2"></textarea></div>

        <div class="text-end">
          <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
          <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  let contador = 1;
  function agregarFila(){
    const tabla = document.querySelector('#tablaMateriales tbody');
    const fila = document.createElement('tr');
    fila.innerHTML = `
      <td><select name="materiales[${contador}][id_material]" class="form-select">
        <option value="">Seleccionar material</option>
        @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
          <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
        @endforeach
      </select></td>
      <td><input type="number" name="materiales[${contador}][cantidad]" class="form-control" min="1"></td>
      <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()">−</button></td>`;
    tabla.appendChild(fila);
    contador++;
  }
  document.getElementById('toggleSidebar').addEventListener('click',()=>document.querySelector('.sidebar').classList.toggle('collapsed'));
  document.getElementById('toggleTheme').addEventListener('click',()=>document.body.classList.toggle('dark-mode'));
</script>
</body>
</html>
