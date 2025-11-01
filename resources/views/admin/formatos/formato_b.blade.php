@extends('layouts.admin')

@section('title', 'Formato B - Equipos e Impresoras')
@section('header_title', 'Formato B - Equipos / Impresoras')
@section('header_subtitle', 'Registro de mantenimiento de equipos e impresoras')

@section('styles')
<style>
.card { border-radius: 10px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05); border: none; }
.card-header { background-color: #399e91; color: white; font-weight: 600; }
.form-control, .form-select { border-radius: 8px; }
.btn-primary { background-color: #399e91; border-color: #399e91; }
.btn-primary:hover { background-color: #2f847a; border-color: #2f847a; }
.btn-outline-secondary { border-radius: 8px; }
.alert-info { background-color: #d1f0eb; border-color: #399e91; color: #25685d; font-weight: 500; }
</style>
@endsection

@section('content')
<div class="alert alert-info mb-4 d-flex align-items-center">
  <i class="fas fa-exclamation-circle me-2"></i>
  Llena todos los campos obligatorios antes de guardar el formato.
</div>

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
          <input type="text" name="descripcion_servicio" class="form-control" required>
        </div>
      </div>

      {{-- ===== COMPUTADORA ===== --}}
      <div id="bloque_computadora">
        <div class="row mb-3">
          <div class="col-md-3">
            <label>Marca</label>
            <input name="marca" id="marca" class="form-control" list="marcaList">
            <datalist id="marcaList"></datalist>
          </div>
          <div class="col-md-3">
            <label>Modelo</label>
            <input name="modelo" class="form-control">
          </div>
          <div class="col-md-3">
            <label>Procesador</label>
            <input name="procesador" id="procesador" class="form-control" list="procesadorList">
            <datalist id="procesadorList"></datalist>
          </div>
          <div class="col-md-3">
            <label>RAM</label>
            <select name="ram" class="form-select">
              @for($i=2;$i<=32;$i+=2)
                <option>{{ $i }} GB</option>
              @endfor
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <label>Disco Duro</label>
            <select name="disco_duro" class="form-select">
              <option value="">Selecciona...</option>
              <option>128 GB SSD</option>
              <option>256 GB SSD</option>
              <option>512 GB SSD</option>
              <option>1 TB HDD</option>
              <option>2 TB HDD</option>
            </select>
          </div>
          <div class="col-md-4">
            <label>Sistema Operativo</label>
            <select name="sistema_operativo" class="form-select">
              <option value="">Selecciona...</option>
              <option>Windows 10</option>
              <option>Windows 11</option>
              <option>Ubuntu Linux</option>
              <option>macOS</option>
              <option>Otro</option>
            </select>
          </div>
          <div class="col-md-2">
            <label>N° Serie</label>
            <input name="numero_serie" class="form-control">
          </div>
          <div class="col-md-2">
            <label>N° Inventario</label>
            <input name="numero_inventario" class="form-control">
          </div>
        </div>
      </div>

      {{-- ===== IMPRESORA ===== --}}
      <div id="bloque_impresora" style="display:none;">
        <div class="row mb-3">
          <div class="col-md-4"><label>Marca</label><input name="marca_impresora" class="form-control"></div>
          <div class="col-md-4"><label>Modelo</label><input name="modelo_impresora" class="form-control"></div>
          <div class="col-md-4">
            <label>Tipo de impresión</label>
            <select name="tipo_impresion" class="form-select">
              <option>Inyección de tinta</option>
              <option>Láser</option>
            </select>
          </div>
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
      <table class="table table-bordered" id="tablaMateriales">
        <thead><tr><th>Material</th><th>Cantidad</th><th>Acciones</th></tr></thead>
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
            <td><button type="button" class="btn btn-sm btn-outline-success agregar-material"><i class="fas fa-plus"></i></button></td>
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
        <a href="{{ route('admin.formatos.create') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Ocultar barra al entrar
  const sidebar = document.getElementById('navigation');
  if (sidebar && !sidebar.classList.contains('collapsed')) {
    sidebar.classList.add('collapsed');
    localStorage.setItem('sidebarCollapsed', true);
  }

  // Mostrar bloque según subtipo
  const subtipo = document.getElementById('subtipo');
  subtipo.addEventListener('change', e => {
    const tipo = e.target.value;
    document.getElementById('bloque_computadora').style.display = tipo === 'Computadora' ? 'block' : 'none';
    document.getElementById('bloque_impresora').style.display = tipo === 'Impresora' ? 'block' : 'none';
  });

  // ===== Autoguardado de Marca y Procesador (LocalStorage) =====
  function updateDatalist(id, key) {
    const datalist = document.getElementById(id + 'List');
    datalist.innerHTML = '';
    const items = JSON.parse(localStorage.getItem(key) || '[]');
    items.forEach(v => {
      const opt = document.createElement('option');
      opt.value = v;
      datalist.appendChild(opt);
    });
  }

  updateDatalist('marca', 'marcas');
  updateDatalist('procesador', 'procesadores');

  ['marca','procesador'].forEach(field => {
    const input = document.getElementById(field);
    input.addEventListener('blur', () => {
      const key = field === 'marca' ? 'marcas' : 'procesadores';
      const val = input.value.trim();
      if (!val) return;
      let arr = JSON.parse(localStorage.getItem(key) || '[]');
      if (!arr.includes(val)) {
        arr.push(val);
        localStorage.setItem(key, JSON.stringify(arr));
        updateDatalist(field, key);
      }
    });
  });

  // ===== Agregar fila de Material =====
  document.addEventListener('click', e => {
    if (e.target.closest('.agregar-material')) {
      const tbody = document.querySelector('#tablaMateriales tbody');
      const index = tbody.querySelectorAll('tr').length;
      const fila = document.createElement('tr');
      fila.innerHTML = `
        <td>
          <select name="materiales[${index}][id_material]" class="form-select">
            <option value="">Seleccionar material</option>
            @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
              <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
            @endforeach
          </select>
        </td>
        <td><input type="number" name="materiales[${index}][cantidad]" class="form-control" min="1"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger eliminar-material"><i class="fas fa-trash"></i></button></td>
      `;
      tbody.appendChild(fila);
    }
    if (e.target.closest('.eliminar-material')) {
      e.target.closest('tr').remove();
    }
  });
});
</script>
@endsection
