@extends('layouts.admin')

@section('title', 'Gestión de Formatos')

@section('content')
<div class="content-wrapper fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-semibold text-primary">
            <i class="fa-solid fa-file-lines me-2"></i> Gestión de Formatos
        </h2>
        <a href="{{ route('admin.formatos.create') }}" class="btn btn-success shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Formato
        </a>
    </div>

    {{-- ===========================
         FILTROS DE BÚSQUEDA
    ============================ --}}
    <form method="GET" action="{{ route('admin.formatos.index') }}" class="card p-3 shadow-sm mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="tipo" class="form-label fw-semibold">Tipo de Formato:</label>
                <select name="tipo" id="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="A" {{ $tipo == 'A' ? 'selected' : '' }}>Formato A</option>
                    <option value="B" {{ $tipo == 'B' ? 'selected' : '' }}>Formato B</option>
                    <option value="C" {{ $tipo == 'C' ? 'selected' : '' }}>Formato C</option>
                    <option value="D" {{ $tipo == 'D' ? 'selected' : '' }}>Formato D</option>
                </select>
            </div>

            @if(Auth::user()->isAdmin())
            <div class="col-md-4">
                <label for="usuario" class="form-label fw-semibold">Usuario:</label>
                <input type="text" name="usuario" id="usuario" class="form-control"
                       placeholder="Buscar por nombre" value="{{ old('usuario', $usuario) }}">
            </div>
            @endif

            <div class="col-md-3">
                <label for="fecha" class="form-label fw-semibold">Fecha:</label>
                <input type="date" name="fecha" id="fecha" class="form-control"
                       value="{{ old('fecha', $fecha) }}">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 shadow-sm">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Filtrar
                </button>
            </div>
        </div>
    </form>

    {{-- ===========================
         TABLA DE FORMATOS
    ============================ --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if($formatos->isEmpty())
                <div class="alert alert-warning text-center mb-0">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    No se encontraron formatos registrados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                @if(Auth::user()->isAdmin())
                                    <th>Usuario</th>
                                @endif
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formatos as $formato)
                                <tr>
                                    <td>{{ $formato->id_servicio }}</td>
                                    <td><span class="badge bg-info text-dark">Formato {{ $formato->tipo }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($formato->fecha)->format('d/m/Y') }}</td>
                                    @if(Auth::user()->isAdmin())
                                        <td>{{ $formato->nombre }}</td>
                                    @endif
                                    <td>
                                        <a href="{{ route('admin.formatos.' . strtolower($formato->tipo) . '.preview', $formato->id_servicio) }}" 
                                           class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fa-solid fa-eye"></i> Ver
                                        </a>
                                        <a href="{{ route('admin.formatos.' . strtolower($formato->tipo) . '.pdf', $formato->id_servicio) }}" 
                                           class="btn btn-sm btn-outline-danger" target="_blank">
                                            <i class="fa-solid fa-file-pdf"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ===========================
         REPORTE GENERAL
    ============================ --}}
    <div class="mt-4 text-end">
        <a href="{{ route('admin.formatos.reporte.general', [
            'tipo' => $tipo,
            'usuario' => $usuario,
            'fecha' => $fecha
        ]) }}" 
        class="btn btn-warning shadow-sm">
            <i class="fa-solid fa-chart-column me-1"></i> Generar Reporte General
        </a>
    </div>
</div>
@endsection
