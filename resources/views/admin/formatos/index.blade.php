@extends('layouts.admin')

@section('title', 'Gestión de Formatos')
@section('header_title', 'Gestión de Formatos')
@section('header_subtitle', 'Histórico y control de documentos generados')

@section('styles')
<style>
    .card-main { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .table thead { background-color: #f8f9fa; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-tipo { font-size: 0.7rem; font-weight: 700; border: 1px solid transparent; }
    .badge-a { background-color: #e0f2f1; color: #399e91; border-color: #399e91; }
    .badge-b { background-color: #e3f2fd; color: #17a2b8; border-color: #17a2b8; }
    .badge-c { background-color: #fff8e1; color: #f59e0b; border-color: #f59e0b; }
    .badge-d { background-color: #fce4ec; color: #e91e63; border-color: #e91e63; }
    .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: 0.2s; }
</style>
@endsection

@section('content')

<div class="container-fluid px-2">

    {{-- HEADER DE ACCIONES --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="bg-primary-subtle text-primary p-3 rounded-3 shadow-sm">
            <i class="fa-solid fa-file-invoice fa-2x"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold">Bandeja de Formatos</h4>
            <p class="text-muted mb-0 small uppercase">Consulta y exportación de servicios concluidos</p>
        </div>
        <a href="{{ route('admin.formatos.create') }}" class="btn btn-primary ms-auto shadow-sm fw-bold px-4 rounded-pill">
            <i class="fa-solid fa-plus me-2"></i> Nuevo Registro
        </a>
    </div>

    {{-- ================= FILTROS MODERNOS ================= --}}
    <div class="card card-main mb-4 border-0 shadow-sm">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.formatos.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">TIPO DE FORMATO</label>
                    <select name="tipo" class="form-select form-select-sm shadow-none">
                        <option value="">— Todos los tipos —</option>
                        <option value="A" @selected($tipo == 'A')>Formato A - Soporte</option>
                        <option value="B" @selected($tipo == 'B')>Formato B - Equipos</option>
                        <option value="C" @selected($tipo == 'C')>Formato C - Redes</option>
                        <option value="D" @selected($tipo == 'D')>Formato D - Entrega</option>
                    </select>
                </div>

                @if(Auth::user()->isAdmin())
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">TÉCNICO / USUARIO</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted small"></i></span>
                        <input type="text" name="usuario" class="form-control border-start-0 shadow-none" placeholder="Buscar por nombre..." value="{{ $usuario }}">
                    </div>
                </div>
                @endif

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">FECHA ESPECÍFICA</label>
                    <input type="date" name="fecha" class="form-control form-control-sm shadow-none" value="{{ $fecha }}">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-dark btn-sm flex-fill fw-bold rounded-pill shadow-sm">
                        <i class="fa-solid fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary btn-sm flex-fill fw-bold rounded-pill">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= TABLA DE RESULTADOS ================= --}}
    <div class="card card-main shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" width="140">Referencia</th>
                        <th>Clasificación</th>
                        <th>Fecha Registro</th>
                        @if(Auth::user()->isAdmin())
                            <th>Responsable Técnico</th>
                        @endif
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($formatos as $formato)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark small">ID #{{ $formato->id_servicio }}</div>
                            <div class="text-muted italic" style="font-size: 0.65rem;">Item {{ $loop->iteration }}</div>
                        </td>

                        <td>
                            @php $t = strtoupper($formato->tipo); @endphp
                            <span class="badge badge-tipo rounded-pill px-3 py-2 badge-{{ strtolower($t) }}">
                                <i class="fas fa-file-alt me-1"></i> FORMATO {{ $t }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="far fa-calendar-alt text-muted"></i>
                                <span class="text-muted small fw-semibold">{{ \Carbon\Carbon::parse($formato->fecha)->format('d/m/Y') }}</span>
                            </div>
                        </td>

                        @if(Auth::user()->isAdmin())
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.7rem; font-weight: bold; color: #399e91; border: 1px solid #dee2e6;">
                                        {{ strtoupper(substr($formato->nombre, 0, 1)) }}
                                    </div>
                                    <span class="small text-dark fw-bold">{{ $formato->nombre }}</span>
                                </div>
                            </td>
                        @endif

                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.formatos.' . strtolower($formato->tipo) . '.preview', $formato->id_servicio) }}"
                                   class="btn-action bg-primary-subtle text-primary border-primary-subtle" title="Ver Vista Previa">
                                    <i class="fa-solid fa-eye small"></i>
                                </a>

                                <a href="{{ route('admin.formatos.' . strtolower($formato->tipo) . '.pdf', $formato->id_servicio) }}"
                                   class="btn-action bg-danger-subtle text-danger border-danger-subtle" target="_blank" title="Generar PDF">
                                    <i class="fa-solid fa-file-pdf small"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted opacity-50">
                                <i class="fa-solid fa-folder-open fa-3x mb-3 d-block"></i>
                                <p class="mb-0 fw-bold">No se encontraron registros</p>
                                <small>Intenta ajustando los filtros de búsqueda</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= REPORTE GENERAL ================= --}}
    <div class="mt-4 text-center">
        <a href="{{ route('admin.formatos.reporte.general', [
                'tipo' => $tipo,
                'usuario' => $usuario,
                'fecha' => $fecha
            ]) }}"
            class="btn btn-warning shadow-sm fw-bold px-5 rounded-pill text-dark border-0">
            <i class="fa-solid fa-chart-column me-2"></i>
            Generar Reporte Consolidado
        </a>
    </div>

</div>
@endsection