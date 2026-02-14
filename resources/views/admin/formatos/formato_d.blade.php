@extends('layouts.admin')

@section('title', 'Formato D - Mantenimiento Personal')
@section('header_title', 'Formato D - Mantenimiento Personal')
@section('header_subtitle', 'Entrega y recepción de equipo institucional')

@section('styles')
<style>
.card-header { background-color: #399e91; color: white; font-weight: 600; }
.form-control, .form-select { border-radius: 8px; }
.btn-primary { background-color: #399e91; border-color: #399e91; }
.btn-primary:hover { background-color: #2f847a; border-color: #2f847a; }
.alert-info { background-color: #d1f0eb; border-color: #399e91; color: #25685d; font-weight: 500; }
</style>
@endsection

@section('content')
<div class="alert alert-info mb-4 d-flex align-items-center">
  <i class="fas fa-exclamation-circle me-2"></i>
  Llena todos los campos obligatorios antes de guardar el formato.
</div>

<div class="card shadow border-0">
  <div class="card-header"><i class="fas fa-tools me-2"></i>Formulario de Formato D</div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.formatos.d.store') }}">
      @csrf

      @if(!empty($id_servicio)) <input type="hidden" name="id_servicio" value="{{ $id_servicio }}"> @endif
      @if(!empty($id_ticket)) <input type="hidden" name="id_ticket" value="{{ $id_ticket }}"> @endif

      <div class="row mb-3">
        <div class="col-md-4">
          <label>Equipo</label>
          <input name="equipo" id="equipo" class="form-control" list="equipoList" required>
          <datalist id="equipoList"></datalist>
        </div>
        <div class="col-md-4">
          <label>Marca</label>
          <input name="marca" id="marca" class="form-control" list="marcaList" required>
          <datalist id="marcaList"></datalist>
        </div>
        <div class="col-md-4">
          <label>Modelo</label>
          <input name="modelo" id="modelo" class="form-control" list="modeloList" required>
          <datalist id="modeloList"></datalist>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4">
          <label>Serie</label>
          <input name="serie" class="form-control">
        </div>
      </div>

      <hr>
      <h6>Firmas y validaciones</h6>
      <div class="row mb-3">
        <div class="col-md-4"><input name="otorgante" placeholder="Otorgante" class="form-control" required></div>
        <div class="col-md-4"><input name="receptor" readonly class="form-control" value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}"></div>
        <div class="col-md-4"><input name="firma_jefe_area" readonly class="form-control" value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}"></div>
      </div>

      <div class="mb-3">
        <label>Observaciones</label>
        <textarea name="observaciones" class="form-control" rows="3"></textarea>
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
  ['equipo', 'marca', 'modelo'].forEach(field => {
    const input = document.getElementById(field);
    input.addEventListener('blur', () => {
      const val = input.value.trim();
      if (!val) return;
      let items = JSON.parse(localStorage.getItem(field + 's') || '[]');
      if (!items.includes(val)) {
        items.push(val);
        localStorage.setItem(field + 's', JSON.stringify(items));
      }
    });
  });
});
</script>
@endsection