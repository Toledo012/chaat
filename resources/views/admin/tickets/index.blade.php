@extends('layouts.admin')

@section('title', 'Tickets')
@section('header_title', 'Gestión de Tickets')
@section('header_subtitle', 'Solicitudes de departamentos y usuarios, asignación y seguimiento')

@section('content')
<div class="container-fluid">

    {{-- HEADER SEGÚN TU IMAGEN --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <i class="fas fa-ticket-alt text-primary fa-2x"></i>
        <div>
            <h4 class="mb-0 fw-bold">Tickets</h4>
            <p class="text-muted mb-0 small">Bandeja principal del Admin</p>
        </div>
    </div>

    {{-- FILTROS OPTIMIZADOS --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0" 
                               placeholder="Folio o título..." value="{{ $qBuscar ?? '' }}">
                    </div>
                </div>
                <div class="col-md-2 text-center">
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Estado (todos)</option>
                        @foreach(['nuevo','asignado','en_proceso','en_espera','completado','cancelado'] as $st)
                            <option value="{{ $st }}" @selected(($qEstado ?? '') === $st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-center">
                    <select name="tipo_formato" class="form-select form-select-sm">
                        <option value="">Formato (todos)</option>
                        @foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $k=>$v)
                            <option value="{{ $k }}" @selected(($qTipo ?? '') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-center">
                    <select name="prioridad" class="form-select form-select-sm">
                        <option value="">Prioridad (todas)</option>
                        @foreach(['baja'=>'Baja','media'=>'Media','alta'=>'Alta'] as $k=>$v)
                            <option value="{{ $k }}" @selected(($qPrioridad ?? '') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-sm btn-primary flex-fill">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">
                        Limpiar
                    </a>
                    <button type="button" class="btn btn-sm btn-success px-3 d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalCrearTicket">
                        <i class="fas fa-plus"></i> <span>Crear Ticket</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- BANDEJA DE TICKETS --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th class="ps-4">Folio</th>
                            <th>Ticket / Registro</th>
                            <th class="text-center">Estado / Prioridad</th>
                            <th>Creado por</th>
                            <th>Tiempos</th>
                            <th>Responsable</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $t)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $t->folio }}</td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $t->titulo }}</div>
                                    <div class="d-flex align-items-center gap-2 mt-1 small text-muted">
                                        <span class="badge bg-secondary-subtle text-secondary border">Formato {{ strtoupper($t->tipo_formato) }}</span>
                                        <span><i class="fas fa-user-edit me-1 small"></i>{{ $t->solicitante ?? 'N/A' }}</span>
                                    </div>
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
                                    <small class="{{ $prioColor }} fw-bold text-uppercase" style="font-size: 0.65rem;">
                                        <i class="fas fa-flag me-1"></i>{{ $t->prioridad }}
                                    </small>
                                </td>
                                <td>
                                    <span class="small fw-semibold text-dark">
                                        <i class="fas fa-pen-nib me-1 text-muted small"></i>{{ $t->creador->username ?? 'Sistema' }}
                                    </span>
                                </td>
                                <td class="small">
                                    <div class="text-muted small"><i class="fas fa-calendar-plus me-1 text-primary small"></i> {{ \Carbon\Carbon::parse($t->created_at)->format('d/m/y H:i') }}</div>
                                    @if(in_array($t->estado, ['completado','cancelado']))
                                        <div class="text-success fw-semibold small mt-1"><i class="fas fa-calendar-check me-1 small"></i> {{ \Carbon\Carbon::parse($t->updated_at)->format('d/m/y H:i') }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($t->asignadoA)
                                        <span class="fw-semibold small text-dark"><i class="fas fa-user-cog me-1 text-primary small"></i>{{ $t->asignadoA->username }}</span>
                                    @else
                                        <span class="text-muted small italic">Sin asignar</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDetalle{{ $t->id_ticket }}">
                                        Gestionar <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- MODAL DE GESTIÓN DETALLADA --}}
                            <div class="modal fade" id="modalDetalle{{ $t->id_ticket }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered shadow-lg">
                                    <div class="modal-content border-0">
                                        <div class="modal-header bg-light border-bottom">
                                            <h6 class="modal-title fw-bold text-dark">Administrar Ticket #{{ $t->folio }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="text-uppercase text-muted small fw-bold mb-1">Título de la Solicitud</h6>
                                                        <h5 class="fw-bold text-dark mb-0">{{ $t->titulo }}</h5>
                                                    </div>
                                                    <span class="badge bg-dark">TIPO {{ strtoupper($t->tipo_formato) }}</span>
                                                </div>
                                                <p class="bg-light p-3 rounded text-muted border small mb-0">{{ $t->descripcion ?? 'Sin descripción adicional.' }}</p>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-6">
                                                    <small class="text-muted d-block small fw-bold text-uppercase">Solicitante:</small>
                                                    <strong class="text-primary small"><i class="fas fa-user me-1 small"></i>{{ $t->solicitante ?? 'No registrado' }}</strong>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted d-block small fw-bold text-uppercase">Registrado por:</small>
                                                    <strong class="text-dark small"><i class="fas fa-pen-nib me-1 small text-muted"></i>{{ $t->creador->username ?? 'Sistema' }}</strong>
                                                </div>
                                            </div>

                                            <hr class="my-4">

                                            {{-- BLOQUE DE ASIGNACIÓN (INDEPENDIENTE) --}}
                                            <form method="POST" action="{{ route('admin.tickets.asignar', $t->id_ticket) }}" class="mb-4">
                                                @csrf
                                                <label class="form-label small fw-bold text-uppercase text-muted">Asignar Responsable</label>
                                                <div class="input-group input-group-sm">
                                                    <select name="asignado_a" class="form-select shadow-sm" @disabled(in_array($t->estado,['cancelado','completado']))>
                                                        <option value="">Seleccionar técnico...</option>
                                                        @foreach($tecnicos as $tec)
                                                            <option value="{{ $tec->id_cuenta }}" @selected($t->id_asignado == $tec->id_cuenta)>{{ $tec->username }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="btn btn-primary fw-bold shadow-sm" @disabled(in_array($t->estado,['cancelado','completado']))>
                                                        <i class="fas fa-user-check me-1"></i>
                                                    </button>
                                                </div>
                                            </form>

                                            <div class="d-grid gap-2">
                                                {{-- BOTÓN EDITAR INFO (NUEVO) --}}
                                                <button class="btn btn-warning py-2 shadow-sm fw-bold mb-1" 
                                                        data-bs-toggle="modal" data-bs-target="#modalEditarInfoAdmin{{ $t->id_ticket }}">
                                                    <i class="fas fa-edit me-1"></i> Editar Información del Ticket
                                                </button>

                                                <a href="{{ route('admin.tickets.completar', $t->id_ticket) }}" 
                                                   class="btn btn-success py-2 shadow-sm fw-bold @if(in_array($t->estado, ['cancelado','completado'])) disabled @endif">
                                                    <i class="fas fa-check-circle me-1"></i> Completar Ticket
                                                </a>

                                                <form method="POST" action="{{ route('admin.tickets.cancelar', $t->id_ticket) }}">
                                                    @csrf
                                                    <button class="btn btn-outline-danger btn-sm w-100 fw-bold border-2" 
                                                            onclick="return confirm('¿Confirmar cancelación definitiva?')"
                                                            @disabled(in_array($t->estado, ['completado','cancelado']))>
                                                        <i class="fas fa-ban me-1"></i> Cancelar Solicitud
                                                    </button>
                                                </form>

                                                @if($t->estado === 'completado' && !empty($t->id_servicio))
                                                    <div class="mt-2 p-3 bg-primary-subtle rounded border border-primary-subtle text-center shadow-sm">
                                                        <p class="small fw-bold text-primary mb-2 text-uppercase">Documentación del Servicio</p>
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            @php
                                                                $tipo = strtolower($t->tipo_formato);
                                                                $prevRoute = route("admin.formatos.{$tipo}.preview", $t->id_servicio);
                                                                $pdfRoute = route("admin.formatos.{$tipo}.pdf", $t->id_servicio);
                                                            @endphp
                                                            <a href="{{ $prevRoute }}" target="_blank" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">
                                                                <i class="fas fa-eye me-1"></i> Ver
                                                            </a>
                                                            <a href="{{ $pdfRoute }}" target="_blank" class="btn btn-sm btn-danger px-4 fw-bold shadow-sm">
                                                                <i class="fas fa-file-pdf me-1"></i> PDF
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL EDITAR INFORMACIÓN (EXCLUSIVO ADMIN) --}}
                            <div class="modal fade" id="modalEditarInfoAdmin{{ $t->id_ticket }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-warning text-dark border-0">
                                            <h6 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Editar Ticket #{{ $t->folio }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.tickets.update', $t->id_ticket) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body p-4 text-start">
                                                <div class="row g-3">
                                                    <div class="col-md-8">
                                                        <label class="form-label small fw-bold text-muted text-uppercase">Título del Asunto *</label>
                                                        <input type="text" name="titulo" class="form-control shadow-sm" required maxlength="255" value="{{ $t->titulo }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold text-muted text-uppercase">Solicitante *</label>
                                                        <input type="text" name="solicitante" class="form-control shadow-sm" required maxlength="150" value="{{ $t->solicitante }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold text-muted text-uppercase">Prioridad</label>
                                                        <select name="prioridad" class="form-select shadow-sm" required>
                                                            <option value="baja" @selected($t->prioridad === 'baja')>🟢 Baja</option>
                                                            <option value="media" @selected($t->prioridad === 'media')>🟡 Media</option>
                                                            <option value="alta" @selected($t->prioridad === 'alta')>🔴 Alta</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold text-muted text-uppercase">Formato</label>
                                                        <select name="tipo_formato" class="form-select shadow-sm" required>
                                                            <option value="a" @selected($t->tipo_formato === 'a')>Formato A</option>
                                                            <option value="b" @selected($t->tipo_formato === 'b')>Formato B</option>
                                                            <option value="c" @selected($t->tipo_formato === 'c')>Formato C</option>
                                                            <option value="d" @selected($t->tipo_formato === 'd')>Formato D</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold text-muted text-uppercase">Estado del Ticket</label>
                                                        <select name="estado" class="form-select shadow-sm" required>
                                                            @foreach(['nuevo','asignado','en_proceso','en_espera','completado','cancelado'] as $st)
                                                                <option value="{{ $st }}" @selected($t->estado === $st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    {{-- Mantenemos el asignado_a oculto o visible aquí también si quieres editarlo desde este form --}}
                                                    <input type="hidden" name="asignado_a" value="{{ $t->id_asignado }}">
                                                    
                                                    <div class="col-12">
                                                        <label class="form-label small fw-bold text-muted text-uppercase">Descripción Detallada</label>
                                                        <textarea name="descripcion" class="form-control shadow-sm" rows="4">{{ $t->descripcion }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-0">
                                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDetalle{{ $t->id_ticket }}">Volver</button>
                                                <button type="submit" class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted opacity-50 small italic">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i> No se encontraron tickets registrados.
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
    <div class="modal-dialog modal-lg modal-dialog-centered shadow">
        <div class="modal-content border-0">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2 text-white"></i>Registrar Nuevo Ticket</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.tickets.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted text-uppercase">Título del Asunto *</label>
                            <input type="text" name="titulo" class="form-control shadow-sm" required value="{{ old('titulo') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Solicitante *</label>
                            <input type="text" name="solicitante" class="form-control shadow-sm" required value="{{ old('solicitante') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Prioridad Inicial</label>
                            <select name="prioridad" class="form-select shadow-sm" required>
                                <option value="baja" @selected(old('prioridad')==='baja')>🟢 Baja</option>
                                <option value="media" @selected(old('prioridad')==='media')>🟡 Media</option>
                                <option value="alta" @selected(old('prioridad')==='alta')>🔴 Alta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Formato Requerido</label>
                            <select name="tipo_formato" class="form-select shadow-sm" required>
                                <option value="a">Formato A</option>
                                <option value="b">Formato B</option>
                                <option value="c">Formato C</option>
                                <option value="d">Formato D</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Descripción de la Falla / Requerimiento</label>
                            <textarea name="descripcion" class="form-control shadow-sm" rows="4">{{ old('descripcion') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 text-center">
                    <button type="submit" class="btn btn-success btn-sm fw-bold px-5 rounded-pill shadow-sm mx-auto">Guardar Ticket</button>
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