<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📋 Formatos registrados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container">

  <div class="card shadow-lg border-0 rounded-3">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Formatos registrados</h4>

      <div class="d-flex gap-2">
        {{-- 📊 Botón de Reporte general --}}
        <a href="{{ route('admin.formatos.reporte.general', request()->query()) }}" class="btn btn-outline-light btn-sm">
          <i class="fas fa-file-pdf me-1"></i>Reporte general
        </a>

        {{-- ➕ Botón de Crear Formato --}}
        <a href="{{ route('admin.formatos.create') }}" class="btn btn-light btn-sm">
          <i class="fas fa-plus me-1"></i>Crear Formato
        </a>
      </div>
    </div>

    <div class="card-body bg-white">
      {{-- 🔍 FILTROS --}}
      <form class="row g-3 mb-4" method="GET" action="{{ route('admin.formatos.index') }}">
        <div class="col-md-3">
          <label class="form-label">Tipo de Formato</label>
          <select name="tipo" class="form-select">
            <option value="">Todos</option>
            <option value="A" {{ $tipo=='A' ? 'selected' : '' }}>A - Soporte</option>
            <option value="B" {{ $tipo=='B' ? 'selected' : '' }}>B - Equipos</option>
            <option value="C" {{ $tipo=='C' ? 'selected' : '' }}>C - Redes</option>
            <option value="D" {{ $tipo=='D' ? 'selected' : '' }}>D - Mantenimiento Personal</option>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Usuario</label>
          <input type="text" name="usuario" class="form-control" value="{{ $usuario }}" placeholder="Buscar por usuario">
        </div>

        <div class="col-md-3">
          <label class="form-label">Fecha</label>
          <input type="date" name="fecha" class="form-control" value="{{ $fecha }}">
        </div>

        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Filtrar</button>
        </div>
      </form>

      {{-- 📊 TABLA DE RESULTADOS --}}
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-success text-dark">
            <tr>
              <th>Tipo</th>
              <th>Usuario</th>
              <th>Fecha</th>
              <th>Ver</th>
              <th>PDF</th>
            </tr>
          </thead>
          <tbody>
            @forelse($formatos as $formato)
              <tr>
                <td>{{ $formato->tipo }}</td>
                <td>{{ $formato->nombre ?? 'Sin usuario' }}</td>
                <td>{{ \Carbon\Carbon::parse($formato->fecha)->format('d/m/Y') }}</td>
                <td>
                  {{-- 👁 Vista previa dinámica según tipo --}}
                  @switch($formato->tipo)
                    @case('A')
                      <a href="{{ route('admin.formatos.a.preview', $formato->id_servicio) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye"></i>
                      </a>
                      @break
                    @case('B')
                      <a href="{{ route('admin.formatos.b.preview', $formato->id_servicio) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye"></i>
                      </a>
                      @break
                    @case('C')
                      <a href="{{ route('admin.formatos.c.preview', $formato->id_servicio) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye"></i>
                      </a>
                      @break
                    @case('D')
                      <a href="{{ route('admin.formatos.d.preview', $formato->id_servicio) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye"></i>
                      </a>
                      @break
                    @default
                      <span class="text-muted">-</span>
                  @endswitch
                </td>
                <td>
                  {{-- 📄 PDF dinámico según tipo --}}
                  @switch($formato->tipo)
                    @case('A')
                      <a href="{{ route('admin.formatos.a.pdf', $formato->id_servicio) }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf"></i>
                      </a>
                      @break
                    @case('B')
                      <a href="{{ route('admin.formatos.b.pdf', $formato->id_servicio) }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf"></i>
                      </a>
                      @break
                    @case('C')
                      <a href="{{ route('admin.formatos.c.pdf', $formato->id_servicio) }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf"></i>
                      </a>
                      @break
                    @case('D')
                      <a href="{{ route('admin.formatos.d.pdf', $formato->id_servicio) }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf"></i>
                      </a>
                      @break
                    @default
                      <span class="text-muted">-</span>
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
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
