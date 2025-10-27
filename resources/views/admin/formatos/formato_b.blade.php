<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formato B - Equipos e Impresoras</title>
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
      <h1 class="h5 mb-0">Formato B - Equipos / Impresoras</h1>
      <span class="subtitle">Registro de mantenimiento de equipos e impresoras</span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <button id="toggleSidebar" class="btn btn-outline-light btn-sm"><i class="fas fa-bars"></i></button>
      <button id="toggleTheme" class="btn btn-outline-light btn-sm"><i class="fas fa-moon"></i></button>
      <div class="user-info">{{ Auth::user()->name ?? 'Administrador' }}</div>
    </div>
  </header>

  {{-- ===== CONTENIDO ===== --}}
  <div class="content-wrapper">
    <div class="card shadow border-0">
      <div class="card-header"><i class="fas fa-desktop me-2"></i>Formulario de Registro</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.formatos.b.store') }}">
          @csrf

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">Subtipo</label>
              <select name="subtipo" id="subtipo" class="form-select">
                <option value="Computadora">Computadora</option>
                <option value="Impresora">Impresora</option>
              </select>
            </div>
            <div class="col-md-8">
              <label class="form-label">Descripción del servicio</label>
              <input type="text" name="descripcion_servicio" class="form-control">
            </div>
          </div>

          {{-- Campos dinámicos según subtipo --}}
          <div id="bloque_computadora">
            <div class="row mb-3">
              <div class="col-md-3"><label>Marca</label><input name="marca" class="form-control"></div>
              <div class="col-md-3"><label>Modelo</label><input name="modelo" class="form-control"></div>
              <div class="col-md-3"><label>Procesador</label><input name="procesador" class="form-control"></div>
              <div class="col-md-3"><label>RAM</label><select name="ram" class="form-select">@for($i=2;$i<=32;$i+=2)<option>{{ $i }} GB</option>@endfor</select></div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4"><label>Disco Duro</label><input name="disco_duro" class="form-control" placeholder="Ej. 500 GB SSD"></div>
              <div class="col-md-4"><label>Sistema Operativo</label><input name="sistema_operativo" class="form-control"></div>
              <div class="col-md-2"><label>N° Serie</label><input name="numero_serie" class="form-control"></div>
              <div class="col-md-2"><label>N° Inventario</label><input name="numero_inventario" class="form-control"></div>
            </div>
          </div>

          <div id="bloque_impresora" style="display:none;">
            <div class="row mb-3">
              <div class="col-md-4"><label>Marca</label><input name="marca_impresora" class="form-control"></div>
              <div class="col-md-4"><label>Modelo</label><input name="modelo_impresora" class="form-control"></div>
              <div class="col-md-4"><label>Tipo de impresión</label><select name="tipo_impresion" class="form-select"><option>Inyección de tinta</option><option>Láser</option></select></div>
            </div>
            <div class="mb-3">
              <label>Diagnóstico</label>
              <textarea name="diagnostico_impresora" class="form-control"></textarea>
            </div>
          </div>

          <div class="mb-3">
            <label>Detalle del servicio realizado</label>
            <textarea name="detalle_realizado" class="form-control" rows="3"></textarea>
          </div>

          <hr>
          <h6><i class="fas fa-cogs me-1"></i>Materiales utilizados</h6>
          <table class="table table-bordered">
            <thead><tr><th>Material</th><th>Cantidad</th></tr></thead>
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
              </tr>
            </tbody>
          </table>

          <div class="row mb-3">
            <div class="col-md-4"><input name="firma_usuario" class="form-control" placeholder="Solicitante"></div>
            <div class="col-md-4"><input name="firma_tecnico" readonly class="form-control" value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}"></div>
            <div class="col-md-4"><input name="firma_jefe_area" readonly class="form-control" value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}"></div>
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
  document.getElementById('subtipo').addEventListener('change', e => {
    const tipo = e.target.value;
    document.getElementById('bloque_computadora').style.display = tipo === 'Computadora' ? 'block' : 'none';
    document.getElementById('bloque_impresora').style.display = tipo === 'Impresora' ? 'block' : 'none';
  });
  document.getElementById('toggleSidebar').addEventListener('click', ()=>document.querySelector('.sidebar').classList.toggle('collapsed'));
  document.getElementById('toggleTheme').addEventListener('click', ()=>{
    document.body.classList.toggle('dark-mode');
    document.getElementById('toggleTheme').innerHTML = document.body.classList.contains('dark-mode') ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
  });
</script>
</body>
</html>
