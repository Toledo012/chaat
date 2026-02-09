@extends('layouts.departamento')

@section('title', 'Tickets Departamento')
@section('header_title', 'Mis Solicitudes')
@section('header_subtitle', 'Seguimiento de tickets enviados al área de sistemas')

@section('content')
<div class="container-fluid">

    {{-- HEADER CON ACCIONES Y FILTROS --}}
    <div class="d-flex align-items-center gap-3 mb-4 px-2">
        <i class="fas fa-ticket-alt text-primary fa-2x"></i>
        <div>
            <h4 class="mb-0 fw-bold">Tickets</h4>
            <p class="text-muted mb-0 small text-uppercase">Bandeja de mi departamento</p>
        </div>
        <button type="button" class="btn btn-primary ms-auto shadow-sm fw-bold btn-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCrearTicket">
            <i class="fas fa-plus me-2"></i> Nuevo Ticket
        </button>
    </div>

    {{-- BARRA DE FILTROS --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0" 
                               placeholder="Buscar por folio, título o solicitante..." value="{{ $qBuscar ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Cualquier estado</option>
                        @foreach(['nuevo','asignado','en_proceso','en_espera','completado','cancelado'] as $st)
                            <option value="{{ $st }}" @selected(($qEstado ?? '') === $st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-sm btn-primary flex-fill fw-bold">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('departamento.tickets.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABLA DE TICKETS --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list me-2 text-primary"></i>Historial de Solicitudes</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th class="ps-4">Folio</th>
                            <th>Título / Solicitante</th>
                            <th class="text-center">Estado / Prioridad</th>
                            <th>Tiempos</th>
                            <th class="text-end pe-4">Gestión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $t)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $t->folio }}</td>
                                <td>
                                    <div class="fw-bold text-dark small text-truncate" style="max-width: 250px;">{{ $t->titulo }}</div>
                                    <div class="text-muted small"><i class="fas fa-user-edit me-1"></i>{{ $t->solicitante }}</div>
                                </td>
                                <td class="text-center">
                                    @php
                                        $stClass = match($t->estado) {
                                            'nuevo' => 'text-bg-primary',
                                            'asignado' => 'text-bg-info',
                                            'en_proceso' => 'text-bg-warning text-dark',
                                            'completado' => 'text-bg-success',
                                            'cancelado' => 'text-bg-danger',
                                            default => 'text-bg-dark',
                                        };
                                        $prioColor = match($t->prioridad) {
                                            'alta' => 'text-danger',
                                            'media' => 'text-warning',
                                            default => 'text-success',
                                        };
                                    @endphp
                                    <span class="badge {{ $stClass }} rounded-pill px-3 mb-1 small d-block mx-auto" style="width: fit-content;">
                                        {{ str_replace('_',' ',$t->estado) }}
                                    </span>
                                    <small class="{{ $prioColor }} fw-bold text-uppercase" style="font-size: 0.6rem;">
                                        <i class="fas fa-flag me-1"></i>{{ $t->prioridad }}
                                    </small>
                                </td>
                                <td class="small">
                                    <div class="text-muted small"><i class="fas fa-calendar-plus me-1 text-primary small"></i> {{ \Carbon\Carbon::parse($t->created_at)->format('d/m/y H:i') }}</div>
                                    @if(in_array($t->estado, ['completado','cancelado']))
                                        <div class="text-success fw-semibold small mt-1"><i class="fas fa-calendar-check me-1 small"></i> {{ \Carbon\Carbon::parse($t->updated_at)->format('d/m/y H:i') }}</div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" 
                                            data-bs-toggle="modal" data-bs-target="#modalDetalle{{ $t->id_ticket }}">
                                        Gestionar <i class="fas fa-chevron-right ms-1"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- MODAL DE GESTIÓN INTERACTIVA --}}
                            <div class="modal fade" id="modalDetalle{{ $t->id_ticket }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered shadow-lg">
                                    <div class="modal-content border-0">
                                        <div class="modal-header bg-light border-bottom">
                                            <h6 class="modal-title fw-bold">Ticket #{{ $t->folio }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-4">
                                                <h6 class="text-uppercase text-muted small fw-bold mb-1">Detalles de la Solicitud</h6>
                                                <h5 class="fw-bold text-dark">{{ $t->titulo }}</h5>
                                                <div class="bg-light p-3 rounded text-muted border small mb-0">
                                                    {{ $t->descripcion ?? 'Sin descripción adicional.' }}
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-6">
                                                    <small class="text-muted d-block small fw-bold text-uppercase">Solicitante:</small>
                                                    <strong class="text-primary small"><i class="fas fa-user me-1 small"></i>{{ $t->solicitante }}</strong>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <small class="text-muted d-block small fw-bold text-uppercase">Estado:</small>
                                                    <span class="badge {{ $stClass }} px-3 rounded-pill">{{ strtoupper($t->estado) }}</span>
                                                </div>
                                            </div>

                                            <div class="d-grid gap-2 border-top pt-4">
                                                {{-- BOTÓN EDITAR (Solo si no está completado/cancelado) --}}
                                                @if(!in_array($t->estado, ['completado','cancelado']))
                                                    <button class="btn btn-warning py-2 fw-bold shadow-sm mb-2" 
                                                            data-bs-toggle="modal" data-bs-target="#modalEditarTicket{{ $t->id_ticket }}">
                                                        <i class="fas fa-edit me-2"></i> Editar Información
                                                    </button>
                                                @endif

                                                {{-- BOTONES DE VER ONLINE Y PDF --}}
                                                @if($t->estado === 'completado' && !empty($t->id_servicio))
                                                    <p class="small fw-bold text-primary mb-2 text-uppercase text-center">Formato Finalizado</p>
                                                    <div class="d-flex gap-2 justify-content-center mb-3">
                                
                                                       
                                                        <a href="{{ route('admin.formatos.'.strtolower($t->tipo_formato).'.pdf', $t->id_servicio) }}" target="_blank" class="btn btn-sm btn-danger px-4 fw-bold shadow-sm">
                                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                                        </a>
                                                    </div>
                                                @endif

                                                {{-- CANCELAR --}}
                                                <form method="POST" action="{{ route('departamento.tickets.cancelar', $t->id_ticket) }}">
                                                    @csrf
                                                    <button class="btn btn-outline-danger btn-sm w-100 fw-bold border-2" 
                                                            onclick="return confirm('¿Confirmar cancelación de la solicitud?')"
                                                            @disabled(in_array($t->estado, ['completado','cancelado']))>
                                                        <i class="fas fa-ban me-1"></i> Cancelar Solicitud
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL EDITAR TICKET (NUEVO) --}}
                            @if(!in_array($t->estado, ['completado','cancelado']))
                            <div class="modal fade" id="modalEditarTicket{{ $t->id_ticket }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-warning text-dark border-0">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Editar Ticket #{{ $t->folio }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('departamento.tickets.update', $t->id_ticket) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body p-4 text-start">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Título de la Solicitud *</label>
                                                    <input type="text" name="titulo" class="form-control shadow-sm" required maxlength="255" value="{{ $t->titulo }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Solicitante *</label>
                                                    <input type="text" name="solicitante" class="form-control shadow-sm" required maxlength="150" value="{{ $t->solicitante }}">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Descripción (Máx. 200 caracteres)</label>
                                                    <textarea name="descripcion" class="form-control shadow-sm" rows="4" maxlength="200">{{ $t->descripcion }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-0">
                                                <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalDetalle{{ $t->id_ticket }}">Volver</button>
                                                <button type="submit" class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm">Actualizar Ticket</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif

                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted opacity-50 small italic">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i> No has enviado solicitudes recientemente.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tickets->hasPages())
            <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-center">
                {!! $tickets->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        @endif
    </div>
</div>

{{-- MODAL CREAR TICKET --}}
<div class="modal fade" id="modalCrearTicket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered shadow">
        <div class="modal-content border-0">
            <form method="POST" action="{{ route('departamento.tickets.store') }}">
                @csrf
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Nueva Solicitud de Sistemas</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Título de la falla o requerimiento *</label>
                        <input type="text" name="titulo" class="form-control shadow-sm border-light-subtle" required maxlength="255" placeholder="Ej: Problemas con el correo institucional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Solicitante *</label>
                        <input type="text" name="solicitante" class="form-control shadow-sm border-light-subtle" required maxlength="150" value="{{ Auth::user()->nombre }}">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase">Descripción del problema (Máx. 200 caracteres) *</label>
                        <textarea name="descripcion" class="form-control shadow-sm border-light-subtle" rows="4" required maxlength="200" placeholder="Describa brevemente lo ocurrido..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="submit" class="btn btn-success btn-sm fw-bold px-5 rounded-pill shadow-sm">Enviar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('modalCrearTicket'));
        modal.show();
    });
</script>
@endif

@endsection