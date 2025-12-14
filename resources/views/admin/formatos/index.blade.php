@extends('layouts.admin')

@section('title', 'Gestión de Formatos')
@section('header_title', 'Gestión de Formatos')
@section('header_subtitle', 'Listado y control de formatos generados')

@section('content')

<div class="container-fluid">

    {{-- CARD CONTENEDORA --}}
    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                <i class="fa-solid fa-file-lines me-2"></i>
                <div>
                    <h5 class="mb-0"> Formatos del Sistema</h5>
                    <small class="text-muted">
                        Consulta, visualiza y exporta los formatos generados
                    </small>
                </div>
            </div>

        <a href="{{ route('admin.formatos.create') }}"
           class="btn btn-success shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Formato
        </a>
    </div>
    


    {{-- ================= FILTROS ================= --}}
    <form method="GET"
          action="{{ route('admin.formatos.index') }}"
          class="card shadow-sm p-3 mb-4">

        <div class="row g-3 align-items-end">

            <div class="col-md-3">
                <label class="form-label fw-semibold">Tipo de Formato</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="A" {{ $tipo == 'A' ? 'selected' : '' }}>Formato A</option>
                    <option value="B" {{ $tipo == 'B' ? 'selected' : '' }}>Formato B</option>
                    <option value="C" {{ $tipo == 'C' ? 'selected' : '' }}>Formato C</option>
                    <option value="D" {{ $tipo == 'D' ? 'selected' : '' }}>Formato D</option>
                </select>
            </div>

            @if(Auth::user()->isAdmin())
            <div class="col-md-4">
                <label class="form-label fw-semibold">Usuario</label>
                <input type="text"
                       name="usuario"
                       class="form-control"
                       placeholder="Buscar por nombre"
                       value="{{ $usuario }}">
            </div>
            @endif

            <div class="col-md-3">
                <label class="form-label fw-semibold">Fecha</label>
                <input type="date"
                       name="fecha"
                       class="form-control"
                       value="{{ $fecha }}">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filtrar
                </button>
            </div>
        </div>
    </form>

    {{-- ================= TABLA ================= --}}
    <div class="card shadow-sm border-0">

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th width="120">#</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        @if(Auth::user()->isAdmin())
                            <th>Usuario</th>
                        @endif
                        <th width="200" class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($formatos as $formato)
                    <tr>
                        {{-- NUM + ID --}}
                        <td class="fw-semibold">
                            {{ $loop->iteration }}
                            <span class="text-muted">
                                · ID {{ $formato->id_servicio }}
                            </span>
                        </td>

                        {{-- TIPO --}}
                        <td>
                            <span class="badge bg-info-subtle text-info px-3 py-2">
                                Formato {{ $formato->tipo }}
                            </span>
                        </td>

                        {{-- FECHA --}}
                        <td class="text-muted">
                            {{ \Carbon\Carbon::parse($formato->fecha)->format('d/m/Y') }}
                        </td>

                        {{-- USUARIO --}}
                        @if(Auth::user()->isAdmin())
                            <td>{{ $formato->nombre }}</td>
                        @endif

                        {{-- ACCIONES --}}
                        <td class="text-center">
                            <a href="{{ route('admin.formatos.' . strtolower($formato->tipo) . '.preview', $formato->id_servicio) }}"
                               class="btn btn-sm btn-outline-primary me-1">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <a href="{{ route('admin.formatos.' . strtolower($formato->tipo) . '.pdf', $formato->id_servicio) }}"
                               class="btn btn-sm btn-outline-danger"
                               target="_blank">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            No se encontraron formatos registrados
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= REPORTE GENERAL ================= --}}
    <div class="mt-4 text-end">
        <a href="{{ route('admin.formatos.reporte.general', [
            'tipo' => $tipo,
            'usuario' => $usuario,
            'fecha' => $fecha
        ]) }}"
        class="btn btn-warning shadow-sm">
            <i class="fa-solid fa-chart-column me-1"></i>
            Generar Reporte General
        </a>
    </div>

</div>
@endsection
