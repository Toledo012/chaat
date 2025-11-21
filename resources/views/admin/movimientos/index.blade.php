@extends('layouts.admin')

@section('title', 'Movimientos (Auditoría del Sistema)')
@section('header_title', 'Auditoría de Movimientos')
@section('header_subtitle', 'Registro detallado de cambios realizados en el sistema')

@section('styles')
<style>
.card-header { background-color: #399e91; color: white; font-weight: 600; }
pre {
  max-height: 300px;
  overflow-y: auto;
  border-radius: 8px;
  padding: 0.75rem;
  background-color: #f8f9fa;
  font-family: 'Courier New', monospace;
  font-size: 0.85rem;
}
.dark-mode pre { background-color: #2b3038; color: #e9ecef; }
.table-success th { background-color: #399e91 !important; color: white; }
.badge { font-size: 0.85rem; }
.details-card {
  border: 1px solid #399e91;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(57,158,145,0.2);
  background: white;
  transition: all 0.3s ease;
  animation: fadeInUp 0.4s ease both;
}
.details-card:hover {
  box-shadow: 0 0 20px rgba(57,158,145,0.35);
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.dark-mode .details-card { background: #1e2227; border-color: #2f847a; }
.dark-mode .details-card:hover { box-shadow: 0 0 20px rgba(63,193,170,0.4); }
</style>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4><i class="fas fa-database me-2 text-primary"></i>Registro de movimientos</h4>
  <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-arrow-left me-1"></i>Volver
  </a>
</div>

{{-- 🔍 FILTROS --}}
<form class="row g-2 mb-3" method="GET">
  <div class="col-md-2">
    <input type="text" list="tablas" class="form-control" name="tabla" placeholder="Tabla" value="{{ request('tabla') }}">
    <datalist id="tablas">
      @foreach(\DB::select('SHOW TABLES') as $t)
        @php $tabla = array_values((array)$t)[0]; @endphp
        <option value="{{ $tabla }}">
      @endforeach
    </datalist>
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
    <input type="text" list="usuarios" class="form-control" name="usuario" placeholder="Usuario" value="{{ request('usuario') }}">
    <datalist id="usuarios">
      @foreach(\App\Models\Usuario::orderBy('nombre')->get() as $u)
        <option value="{{ $u->name }}">
      @endforeach
    </datalist>
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
            <div class="row g-3">
              <div class="col-md-6">
                <div class="details-card p-3 border-start border-danger border-3">
                  <h6 class="text-danger mb-2"><i class="fas fa-arrow-left me-1"></i>ANTES</h6>
                  <pre>{{ json_encode(json_decode($m->datos_anteriores ?? 'null', true), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
              </div>
              <div class="col-md-6">
                <div class="details-card p-3 border-start border-success border-3">
                  <h6 class="text-success mb-2"><i class="fas fa-arrow-right me-1"></i>DESPUÉS</h6>
                  <pre>{{ json_encode(json_decode($m->datos_nuevos ?? 'null', true), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
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
  <div class="card-footer bg-light">
    {{ $movimientos->links() }}
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Sidebar colapsada al entrar
  const sidebar = document.getElementById('navigation');
  if (sidebar && !sidebar.classList.contains('collapsed')) {
    sidebar.classList.add('collapsed');
    localStorage.setItem('sidebarCollapsed', true);
  }
});
</script>
@endsection
