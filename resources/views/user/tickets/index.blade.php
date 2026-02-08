@extends('layouts.admin')

@section('title', 'Mis Tickets')
@section('header_title', 'Gestión de Mis Tickets')
@section('header_subtitle', 'Bandeja de atención técnica y seguimiento de solicitudes asignadas')

@section('content')
<div class="container-fluid">

    {{-- ENCABEZADO ESTILO ADMIN --}}
    <div class="d-flex align-items-center gap-3 mb-4 px-2">
        <i class="fas fa-ticket-alt text-primary fa-2x"></i>
        <div>
            <h4 class="mb-0 fw-bold">Tickets</h4>
            <p class="text-muted mb-0 small text-uppercase">Bandeja de trabajo del usuario</p>
        </div>
        <button type="button" class="btn btn-primary ms-auto shadow-sm fw-bold btn-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCrearTicket">
            <i class="fas fa-plus me-2"></i> Crear Ticket
        </button>
    </div>

    {{-- TICKETS DISPONIBLES (PARA TOMAR) --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-inbox me-2 text-primary"></i>Tickets disponibles para atención
            </h6>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 rounded-pill small">
                {{ $disponibles->count() }} Disponibles
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th class="ps-4">Folio</th>
                            <th>Título / Solicitante</th>
                            <th>Formato</th>
                            <th>Creado por</th>
                            <th>Fecha Registro</th>
                            <th class="text-end pe-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($disponibles as $t)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $t->folio }}</td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $t->titulo }}</div>
                                    <div class="text-muted small"><i class="fas fa-user-edit me-1 small"></i>{{ $t->solicitante }}</div>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary border px-2">TIPO {{ strtoupper($t->tipo_formato) }}</span></td>
                                <td>
                                    <span class="small fw-semibold text-dark">
                                        <i class="fas fa-pen-nib me-1 text-muted small"></i>{{ $t->creador->username ?? 'Sistema' }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <div class="text-nowrap"><i class="far fa-calendar-plus me-1"></i> {{ \Carbon\Carbon::parse($t->created_at)->format('d/m/y H:i') }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST" action="{{ route('user.tickets.tomar', $t->id_ticket) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                                            Tomar <i class="fas fa-hand-paper ms-1"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small italic">No hay tickets libres por ahora.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MIS TICKETS (ASIGNADOS) --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-user-check me-2 text-primary"></i>Mi bandeja de trabajo</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th class="ps-4">Folio</th>
                            <th>Título / Información</th>
                            <th>Estado</th>
                            <th>Tiempos</th>
                            <th class="text-end pe-4">Gestión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($misTickets as $t)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $t->folio }}</td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $t->titulo }}</div>
                                    <div class="text-muted small">Formato {{ strtoupper($t->tipo_formato) }}</div>
                                </td>
                                <td>
                                    @php
                                        $stClass = match($t->estado) {
                                            'en_proceso' => 'text-bg-warning text-dark',
                                            'completado' => 'text-bg-success',
                                            'cancelado' => 'text-bg-danger',
                                            default => 'text-bg-info',
                                        };
                                    @endphp
                                    <span class="badge {{ $stClass }} rounded-pill px-3" style="font-size: 0.65rem;">
                                        {{ strtoupper(str_replace('_',' ',$t->estado)) }}
                                    </span>
                                </td>
                                <td class="small">
                                    <div class="text-muted small"><i class="fas fa-calendar-plus me-1 text-primary small"></i> <strong>Creado:</strong> {{ \Carbon\Carbon::parse($t->created_at)->format('d/m/y H:i') }}</div>
                                    @if(in_array($t->estado, ['completado','cancelado']))
                                        <div class="text-success fw-semibold small mt-1"><i class="fas fa-calendar-check me-1 small"></i> <strong>Concluido:</strong> {{ \Carbon\Carbon::parse($t->updated_at)->format('d/m/y H:i') }}</div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" 
                                            data-bs-toggle="modal" data-bs-target="#modalGestionUser{{ $t->id_ticket }}">
                                        Gestionar <i class="fas fa-chevron-right ms-1"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- MODAL DE GESTIÓN INTERACTIVA --}}
                            <div class="modal fade" id="modalGestionUser{{ $t->id_ticket }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered shadow-lg">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-light border-bottom">
                                            <h6 class="modal-title fw-bold">Detalles Ticket #{{ $t->folio }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-4">
                                                <h6 class="text-uppercase text-muted small fw-bold mb-1">Título de la Solicitud</h6>
                                                <h5 class="fw-bold text-dark">{{ $t->titulo }}</h5>
                                                <div class="bg-light p-3 rounded text-muted border small mb-0">{{ $t->descripcion ?? 'Sin descripción adicional.' }}</div>
                                            </div>

                                            <div class="row g-3 mb-4 text-start">
                                                <div class="col-6">
                                                    <small class="text-muted d-block small fw-bold text-uppercase">Solicitante:</small>
                                                    <strong class="text-primary small"><i class="fas fa-user me-1 small"></i>{{ $t->solicitante }}</strong>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block small fw-bold text-uppercase">Tipo Formato:</small>
                                                    <span class="badge bg-dark">TIPO {{ strtoupper($t->tipo_formato) }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block small fw-bold text-uppercase">Fecha Apertura:</small>
                                                    <span class="small fw-semibold text-dark">{{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') }}</span>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <small class="text-muted d-block small fw-bold text-uppercase text-end">Fecha Cierre:</small>
                                                    <span class="small fw-semibold text-dark">
                                                        {{ in_array($t->estado, ['completado','cancelado']) ? \Carbon\Carbon::parse($t->updated_at)->format('d/m/Y H:i') : 'En proceso' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="d-grid gap-2 border-top pt-4">
                                                @if(!in_array($t->estado, ['cancelado','completado']))
                                                    <button class="btn btn-warning py-2 fw-bold shadow-sm mb-1" 
                                                            data-bs-toggle="modal" data-bs-target="#modalEditarTicketUser{{ $t->id_ticket }}">
                                                        <i class="fas fa-edit me-2"></i> Editar Información
                                                    </button>
                                                    
                                                    <a href="{{ route('user.tickets.completar', $t->id_ticket) }}" class="btn btn-primary py-2 fw-bold shadow-sm">
                                                        <i class="fas fa-clipboard-check me-2"></i> Completar y Generar Formato
                                                    </a>
                                                @else
                                                    @if($t->estado === 'completado' && !empty($t->id_servicio))
                                                        <div class="p-3 bg-primary-subtle rounded border border-primary-subtle text-center">
                                                            <p class="small fw-bold text-primary mb-2 text-uppercase">Documentación del Servicio</p>
                                                            <div class="d-flex gap-2 justify-content-center">
                                                                <a href="{{ route('admin.formatos.'.strtolower($t->tipo_formato).'.preview', $t->id_servicio) }}" target="_blank" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">
                                                                    <i class="fas fa-eye me-1"></i> Ver Online
                                                                </a>
                                                                <a href="{{ route('admin.formatos.'.strtolower($t->tipo_formato).'.pdf', $t->id_servicio) }}" target="_blank" class="btn btn-sm btn-danger px-4 fw-bold shadow-sm">
                                                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL EDITAR TICKET (USUARIO) --}}
                            @if(!in_array($t->estado, ['completado','cancelado']))
                            <div class="modal fade" id="modalEditarTicketUser{{ $t->id_ticket }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-warning text-dark border-0">
                                            <h6 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Editar Ticket #{{ $t->folio }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('user.tickets.update', $t->id_ticket) }}">
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
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Tipo de Formato *</label>
                                                    <select name="tipo_formato" class="form-select shadow-sm" required>
                                                        <option value="a" @selected($t->tipo_formato === 'a')>Formato A</option>
                                                        <option value="b" @selected($t->tipo_formato === 'b')>Formato B</option>
                                                        <option value="c" @selected($t->tipo_formato === 'c')>Formato C</option>
                                                        <option value="d" @selected($t->tipo_formato === 'd')>Formato D</option>
                                                    </select>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold text-muted text-uppercase">Descripción Detallada (Máx 200)</label>
                                                    <textarea name="descripcion" class="form-control shadow-sm" rows="4" maxlength="200">{{ $t->descripcion }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-0">
                                                <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalGestionUser{{ $t->id_ticket }}">Volver</button>
                                                <button type="submit" class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif

                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted opacity-50 small italic">No tienes tickets asignados en tu bandeja personal.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREAR TICKET --}}
<div class="modal fade" id="modalCrearTicket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered shadow">
        <div class="modal-content border-0">
            <form method="POST" action="{{ route('user.tickets.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2 text-white"></i>Registrar Solicitud</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted text-uppercase">Título del Ticket *</label>
                            <input type="text" name="titulo" class="form-control shadow-sm border-light-subtle" required placeholder="Ej: Falla en equipo de red">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Solicitante *</label>
                            <input type="text" name="solicitante" class="form-control shadow-sm border-light-subtle" required placeholder="Nombre del solicitante">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tipo de Formato Requerido</label>
                            <select name="tipo_formato" class="form-select shadow-sm border-light-subtle" required>
                                <option value="">Seleccionar formato...</option>
                                <option value="a">Formato A</option>
                                <option value="b">Formato B</option>
                                <option value="c">Formato C</option>
                                <option value="d">Formato D</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Descripción Detallada (Máx 200)</label>
                            <textarea name="descripcion" class="form-control shadow-sm border-light-subtle" rows="4" maxlength="200" placeholder="Explique brevemente los detalles del requerimiento..."></textarea>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted text-center border-top pt-3">
                        <i class="fas fa-info-circle me-1"></i> La prioridad de este ticket será establecida por el administrador.
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 text-center">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-5 rounded-pill shadow-sm mx-auto">Guardar Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection