@extends('layouts.admin')

@section('title', 'Movimientos')
@section('header_title', 'Movimientos del Sistema')
@section('header_subtitle', 'Auditoría y registro de acciones')

@section('content')
<div class="container-fluid">

    {{-- CARD PRINCIPAL --}}
    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-list fa-lg text-primary"></i>
                <div>
                    <h5 class="mb-0">Historial de Movimientos</h5>
                    <small class="text-muted">
                        Registro de inserciones, modificaciones y eliminaciones
                    </small>
                </div>
            </div>

            {{-- BOTÓN EXPORTAR (MISMA RUTA) --}}
<div class="d-flex justify-content-end mb-3">
  <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.movimientos.index', array_merge(request()->query(), ['export' => 1, 'autoprint' => 0])) }}" target="_blank">
    <i class="fas fa-file-pdf me-1"></i> Exportar PDF
  </a>
</div>
        </div>

        {{-- FILTROS (MISMA LÓGICA) --}}
        <div class="card-body border-bottom">
            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label">Tabla</label>
                    <select name="tabla" class="form-select">
                        <option value="">Todas</option>
                        <option value="Usuarios" {{ request('tabla')=='Usuarios' ? 'selected' : '' }}>Usuarios</option>
                        <option value="Cuentas" {{ request('tabla')=='Cuentas' ? 'selected' : '' }}>Cuentas</option>
                        <option value="Servicios" {{ request('tabla')=='Servicios' ? 'selected' : '' }}>Servicios</option>
                        <option value="Materiales_Utilizados" {{ request('tabla')=='Materiales_Utilizados' ? 'selected' : '' }}>Materiales</option>
                        <option value="Formato_A" {{ request('tabla')=='Formato_A' ? 'selected' : '' }}>Formato A</option>
                        <option value="Formato_B" {{ request('tabla')=='Formato_B' ? 'selected' : '' }}>Formato B</option>
                        <option value="Formato_C" {{ request('tabla')=='Formato_C' ? 'selected' : '' }}>Formato C</option>
                        <option value="Formato_D" {{ request('tabla')=='Formato_D' ? 'selected' : '' }}>Formato D</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Acción</label>
                    <select name="accion" class="form-select">
                        <option value="">Todas</option>
                        <option value="INSERT" {{ request('accion')=='INSERT' ? 'selected' : '' }}>INSERT</option>
                        <option value="UPDATE" {{ request('accion')=='UPDATE' ? 'selected' : '' }}>UPDATE</option>
                        <option value="DELETE" {{ request('accion')=='DELETE' ? 'selected' : '' }}>DELETE</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Rango de fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
                </div>

                <div class="col-md-4 text-end">
                    <button class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.movimientos.index') }}" class="btn btn-secondary">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- TABLA --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tabla</th>
                        <th>Acción</th>
                        <th>ID Registro</th>
                        <th>Usuario</th>
                        <th>Detalles</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($movimientos as $mov)
                        <tr>
                            <td class="text-muted">
                                {{ $mov->fecha }}
                            </td>

                            <td>
                                <span class="fw-semibold">{{ $mov->tabla }}</span>
                            </td>

                            <td>
                                @if($mov->accion === 'INSERT')
                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                        INSERT
                                    </span>
                                @elseif($mov->accion === 'UPDATE')
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                        UPDATE
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                        DELETE
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                    {{ $mov->id_registro }}
                                </span>
                            </td>

                            <td>
                                {{ $mov->cuenta->username ?? 'Sistema' }}
                            </td>

                            <td>
                                <details>
                                    <summary class="text-primary" style="cursor:pointer">
                                        Ver datos
                                    </summary>
                                    <pre class="mt-2 small text-muted">
{{ json_encode([
    'antes' => $mov->datos_anteriores,
    'despues' => $mov->datos_nuevos
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                    </pre>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay movimientos registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN --}}
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando {{ $movimientos->count() }} de {{ $movimientos->total() }} registros
            </small>

            {{ $movimientos->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
