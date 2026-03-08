@extends('layouts.admin')

@section('title', 'Tickets')
@section('header_title', 'Gestión de Tickets')
@section('header_subtitle', 'Solicitudes de departamentos y usuarios, asignación y seguimiento')

@section('content')
    <div class="container-fluid">

        <div class="d-flex align-items-center gap-2 mb-4">
            <i class="fas fa-ticket-alt text-primary fa-2x"></i>
            <div>
                <h4 class="mb-0 fw-bold">Tickets</h4>
                <p class="text-muted mb-0 small">Bandeja principal del Admin</p>
            </div>
        </div>

        {{-- FILTROS --}}
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
                        <button type="button" class="btn btn-sm btn-success px-3 d-flex align-items-center gap-1"
                                data-bs-toggle="modal" data-bs-target="#modalCrearTicket">
                            <i class="fas fa-plus"></i> <span>Crear Ticket</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- BANDEJA --}}
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
                        <tbody id="ticketsTableBody">
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
                                            'nuevo'      => 'text-bg-primary',
                                            'asignado'   => 'text-bg-info',
                                            'en_proceso' => 'text-bg-warning text-dark',
                                            'completado' => 'text-bg-success',
                                            'cancelado'  => 'text-bg-danger',
                                            default      => 'text-bg-dark',
                                        };
                                        $prioColor = match($t->prioridad) {
                                            'alta'  => 'text-danger',
                                            'media' => 'text-warning',
                                            default => 'text-success',
                                        };
                                    @endphp
                                    <span class="badge {{ $stClass }} rounded-pill px-3 mb-1 small d-block mx-auto" style="width:fit-content;">
                                        {{ str_replace('_',' ',$t->estado) }}
                                    </span>
                                    <small class="{{ $prioColor }} fw-bold text-uppercase" style="font-size:0.65rem;">
                                        <i class="fas fa-flag me-1"></i>{{ $t->prioridad }}
                                    </small>
                                </td>
                                <td>
                                    <span class="small fw-semibold text-dark">
                                        <i class="fas fa-pen-nib me-1 text-muted small"></i>{{ $t->creador->username ?? 'Sistema' }}
                                    </span>
                                </td>
                                <td class="small">
                                    <div class="text-muted small">
                                        <i class="fas fa-calendar-plus me-1 text-primary small"></i>
                                        {{ \Carbon\Carbon::parse($t->created_at)->timezone('America/Mexico_City')->format('d/m/Y h:i A') }}
                                    </div>
                                    @if(in_array($t->estado, ['completado','cancelado']))
                                        <div class="text-success fw-semibold small mt-1">
                                            <i class="fas fa-calendar-check me-1 small"></i>
                                            {{ \Carbon\Carbon::parse($t->updated_at)->timezone('America/Mexico_City')->format('d/m/Y h:i A') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($t->asignadoA)
                                        <span class="fw-semibold small text-dark">
                                            <i class="fas fa-user-cog me-1 text-primary small"></i>{{ $t->asignadoA->username }}
                                        </span>
                                    @else
                                        <span class="text-muted small italic">Sin asignar</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#modalDetalle{{ $t->id_ticket }}">
                                        Gestionar <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </td>
                            </tr>
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

            {{-- Contenedor de modales dinámicos --}}
            <div id="ticketsModalsContainer"></div>

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
                                <label class="form-label small fw-bold text-muted text-uppercase">Departamento *</label>
                                <div class="input-group">
                                    <select id="selectDepartamento" name="id_departamento" class="form-select shadow-sm" required>
                                        <option value="">Selecciona un departamento</option>
                                        @foreach($departamentos as $d)
                                            <option value="{{ $d->id_departamento }}" @selected(old('id_departamento') == $d->id_departamento)>
                                                {{ $d->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-light fw-bold"
                                            style="border-color:#fff;color:#fff;background:#2c7a70"
                                            data-bs-toggle="modal" data-bs-target="#modalCrearDepartamento">
                                        <i class="fas fa-plus me-1"></i> Nuevo
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">Si no existe, crea uno aquí mismo.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Solicitante *</label>
                                <input type="text" name="solicitante" class="form-control shadow-sm" required value="{{ old('solicitante') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Prioridad Inicial</label>
                                <select name="prioridad" class="form-select shadow-sm" required>
                                    <option value="baja"  @selected(old('prioridad')==='baja') >🟢 Baja</option>
                                    <option value="media" @selected(old('prioridad','media')==='media')>🟡 Media</option>
                                    <option value="alta"  @selected(old('prioridad')==='alta') >🔴 Alta</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Formato Requerido</label>
                                <select name="tipo_formato" class="form-select shadow-sm" required>
                                    @foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $k=>$v)
                                        <option value="{{ $k }}" @selected(old('tipo_formato')===$k)>Formato {{ $v }}</option>
                                    @endforeach
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

    {{-- MODAL CREAR DEPARTAMENTO --}}
    <div class="modal fade" id="modalCrearDepartamento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-building me-2"></i> Registrar Departamento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrearDepartamento">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre *</label>
                            <input type="text" name="nombre" class="form-control shadow-sm" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Descripción</label>
                            <textarea name="descripcion" class="form-control shadow-sm" rows="3"></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="activoDepto" name="activo" checked>
                            <label class="form-check-label" for="activoDepto">Activo</label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success btn-sm fw-bold px-4 rounded-pill shadow-sm">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('modalCrearTicket')).show();
            });
        </script>
    @endif

@endsection

@section('scripts')
    <script>
        const TECNICOS     = @json($tecnicos->map(fn($t) => ['id_cuenta' => $t->id_cuenta, 'username' => $t->username])->values());
        const DEPARTAMENTOS = @json($departamentos->map(fn($d) => ['id_departamento' => $d->id_departamento, 'nombre' => $d->nombre])->values());
        const CSRF          = '{{ csrf_token() }}';

        // ── Helpers ──────────────────────────────────────────────────────────────

        function esc(v) {
            if (v === null || v === undefined) return '';
            return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
        }

        function fechaMX(iso) {
            if (!iso) return '';
            const f = new Date(iso);
            return isNaN(f) ? esc(iso) : f.toLocaleDateString('es-MX') + ' ' + f.toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit', hour12:true });
        }

        function estadoClass(e) {
            return { nuevo:'text-bg-primary', asignado:'text-bg-info', en_proceso:'text-bg-warning text-dark', completado:'text-bg-success', cancelado:'text-bg-danger' }[e] ?? 'text-bg-dark';
        }

        function prioClass(p) {
            return { alta:'text-danger', media:'text-warning', baja:'text-success' }[p] ?? 'text-success';
        }

        function opcionesTecnicos(asignadoId) {
            return '<option value="">Seleccionar técnico...</option>' +
                TECNICOS.map(t => `<option value="${t.id_cuenta}" ${Number(asignadoId) === Number(t.id_cuenta) ? 'selected' : ''}>${esc(t.username)}</option>`).join('');
        }

        function opcionesDeptos(selId) {
            return '<option value="">Selecciona un departamento</option>' +
                DEPARTAMENTOS.map(d => `<option value="${d.id_departamento}" ${Number(selId) === Number(d.id_departamento) ? 'selected' : ''}>${esc(d.nombre)}</option>`).join('');
        }

        // ── Destruir instancias Bootstrap de modales dinámicos ───────────────────
        // Esto es la clave de la opción C: limpiar ANTES de reemplazar el DOM

        function destruirModalesBootstrap() {
            document.querySelectorAll('#ticketsModalsContainer .modal').forEach(el => {
                // Si el modal está abierto, cerrarlo sin animación primero
                const instance = bootstrap.Modal.getInstance(el);
                if (instance) {
                    instance.dispose(); // libera la instancia completamente
                }
            });
        }

        // ── Renderizar filas ──────────────────────────────────────────────────────

        function renderizarFilas(tickets) {
            const tbody = document.getElementById('ticketsTableBody');
            if (!tbody) return;

            if (!Array.isArray(tickets) || tickets.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted opacity-50 small italic">
                <i class="fas fa-inbox fa-3x mb-3 d-block"></i> No se encontraron tickets registrados.</td></tr>`;
                return;
            }

            tbody.innerHTML = tickets.map(t => {
                const estado   = t.estado ?? '';
                const prioridad = t.prioridad ?? 'baja';
                const fechaCierre = ['completado','cancelado'].includes(estado)
                    ? `<div class="text-success fw-semibold small mt-1"><i class="fas fa-calendar-check me-1 small"></i>${fechaMX(t.updated_at)}</div>` : '';

                return `
            <tr>
                <td class="ps-4 fw-bold text-primary">#${esc(t.folio)}</td>
                <td>
                    <div class="fw-bold text-dark small">${esc(t.titulo)}</div>
                    <div class="d-flex align-items-center gap-2 mt-1 small text-muted">
                        <span class="badge bg-secondary-subtle text-secondary border">Formato ${esc(String(t.tipo_formato ?? '').toUpperCase())}</span>
                        <span><i class="fas fa-user-edit me-1 small"></i>${esc(t.solicitante ?? 'N/A')}</span>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge ${estadoClass(estado)} rounded-pill px-3 mb-1 small d-block mx-auto" style="width:fit-content;">
                        ${esc(estado.replaceAll('_',' '))}
                    </span>
                    <small class="${prioClass(prioridad)} fw-bold text-uppercase" style="font-size:0.65rem;">
                        <i class="fas fa-flag me-1"></i>${esc(prioridad)}
                    </small>
                </td>
                <td><span class="small fw-semibold text-dark"><i class="fas fa-pen-nib me-1 text-muted small"></i>${esc(t.creado_por?.username ?? 'Sistema')}</span></td>
                <td class="small">
                    <div class="text-muted small"><i class="fas fa-calendar-plus me-1 text-primary small"></i>${fechaMX(t.created_at)}</div>
                    ${fechaCierre}
                </td>
                <td>${t.asignado_a?.username
                    ? `<span class="fw-semibold small text-dark"><i class="fas fa-user-cog me-1 text-primary small"></i>${esc(t.asignado_a.username)}</span>`
                    : `<span class="text-muted small italic">Sin asignar</span>`}
                </td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#modalDetalle${t.id_ticket}">
                        Gestionar <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </td>
            </tr>`;
            }).join('');
        }

        // ── Renderizar modales ────────────────────────────────────────────────────

        function renderizarModales(tickets) {
            const contenedor = document.getElementById('ticketsModalsContainer');
            if (!contenedor) return;

            contenedor.innerHTML = tickets.map(t => {
                const bloqueado   = ['cancelado','completado'].includes(t.estado);
                const dis         = bloqueado ? 'disabled' : '';
                const tipo        = String(t.tipo_formato ?? '').toLowerCase();
                const prevUrl     = t.id_servicio ? `/admin/formatos/${tipo}/${t.id_servicio}/preview` : '';
                const pdfUrl      = t.id_servicio ? `/admin/formatos/${tipo}/${t.id_servicio}/pdf` : '';

                return `
            {{-- Modal gestionar --}}
                <div class="modal fade" id="modalDetalle${t.id_ticket}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered shadow-lg">
                    <div class="modal-content border-0">
                        <div class="modal-header bg-light border-bottom">
                            <h6 class="modal-title fw-bold text-dark">Administrar Ticket #${esc(t.folio)}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-uppercase text-muted small fw-bold mb-1">Título de la Solicitud</h6>
                                        <h5 class="fw-bold text-dark mb-0">${esc(t.titulo)}</h5>
                                    </div>
                                    <span class="badge bg-dark">TIPO ${esc(String(t.tipo_formato ?? '').toUpperCase())}</span>
                                </div>
                                <p class="bg-light p-3 rounded text-muted border small mb-0">${esc(t.descripcion ?? 'Sin descripción adicional.')}</p>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <small class="text-muted d-block small fw-bold text-uppercase">Solicitante:</small>
                                    <strong class="text-primary small"><i class="fas fa-user me-1 small"></i>${esc(t.solicitante ?? 'No registrado')}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block small fw-bold text-uppercase">Registrado por:</small>
                                    <strong class="text-dark small"><i class="fas fa-pen-nib me-1 small text-muted"></i>${esc(t.creado_por?.username ?? 'Sistema')}</strong>
                                </div>
                            </div>
                            <hr class="my-4">
                            <form method="POST" action="/admin/tickets/${t.id_ticket}/asignar" class="mb-4">
                                <input type="hidden" name="_token" value="${CSRF}">
                                <label class="form-label small fw-bold text-uppercase text-muted">Asignar Responsable</label>
                                <div class="input-group input-group-sm">
                                    <select name="asignado_a" class="form-select shadow-sm" ${dis}>${opcionesTecnicos(t.asignado_a?.id_cuenta)}</select>
                                    <button class="btn btn-primary fw-bold shadow-sm" ${dis}><i class="fas fa-user-check me-1"></i></button>
                                </div>
                            </form>
                            <div class="d-grid gap-2">
                                <button class="btn btn-warning py-2 shadow-sm fw-bold mb-1"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarInfoAdmin${t.id_ticket}">
                                    <i class="fas fa-edit me-1"></i> Editar Información del Ticket
                                </button>
                                <a href="/admin/tickets/${t.id_ticket}/completar"
                                   class="btn btn-success py-2 shadow-sm fw-bold ${bloqueado ? 'disabled' : ''}">
                                    <i class="fas fa-check-circle me-1"></i> Completar Ticket
                                </a>
                                <form method="POST" action="/admin/tickets/${t.id_ticket}/cancelar">
                                    <input type="hidden" name="_token" value="${CSRF}">
                                    <button class="btn btn-outline-danger btn-sm w-100 fw-bold border-2"
                                            onclick="return confirm('¿Confirmar cancelación definitiva?')" ${dis}>
                                        <i class="fas fa-ban me-1"></i> Cancelar Solicitud
                                    </button>
                                </form>
                                ${(t.estado === 'completado' && t.id_servicio) ? `
                                <div class="mt-2 p-3 bg-primary-subtle rounded border border-primary-subtle text-center shadow-sm">
                                    <p class="small fw-bold text-primary mb-2 text-uppercase">Documentación del Servicio</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="${prevUrl}" target="_blank" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm"><i class="fas fa-eye me-1"></i> Ver</a>
                                        <a href="${pdfUrl}" target="_blank" class="btn btn-sm btn-danger px-4 fw-bold shadow-sm"><i class="fas fa-file-pdf me-1"></i> PDF</a>
                                    </div>
                                </div>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal editar --}}
                <div class="modal fade" id="modalEditarInfoAdmin${t.id_ticket}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-warning text-dark border-0">
                            <h6 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Editar Ticket #${esc(t.folio)}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="/admin/tickets/${t.id_ticket}">
                            <input type="hidden" name="_token" value="${CSRF}">
                            <input type="hidden" name="_method" value="PUT">
                            <div class="modal-body p-4 text-start">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Título *</label>
                                        <input type="text" name="titulo" class="form-control shadow-sm" required maxlength="255" value="${esc(t.titulo)}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Solicitante *</label>
                                        <input type="text" name="solicitante" class="form-control shadow-sm" required maxlength="150" value="${esc(t.solicitante)}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Departamento *</label>
                                        <select name="id_departamento" class="form-select shadow-sm" required>${opcionesDeptos(t.id_departamento)}</select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Prioridad</label>
                                        <select name="prioridad" class="form-select shadow-sm" required>
                                            <option value="baja"  ${t.prioridad==='baja'  ? 'selected':''}>🟢 Baja</option>
                                            <option value="media" ${t.prioridad==='media' ? 'selected':''}>🟡 Media</option>
                                            <option value="alta"  ${t.prioridad==='alta'  ? 'selected':''}>🔴 Alta</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Formato</label>
                                        <select name="tipo_formato" class="form-select shadow-sm" required>
                                            ${['a','b','c','d'].map(k => `<option value="${k}" ${t.tipo_formato===k?'selected':''}>Formato ${k.toUpperCase()}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Estado</label>
                                        <select name="estado" class="form-select shadow-sm" required>
                                            ${['nuevo','asignado','en_proceso','en_espera','completado','cancelado'].map(s =>
                    `<option value="${s}" ${t.estado===s?'selected':''}>${s.replaceAll('_',' ').replace(/^\w/,c=>c.toUpperCase())}</option>`
                ).join('')}
                                        </select>
                                    </div>
                                    <input type="hidden" name="asignado_a" value="${t.asignado_a?.id_cuenta ?? ''}">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Descripción</label>
                                        <textarea name="descripcion" class="form-control shadow-sm" rows="4">${esc(t.descripcion ?? '')}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-0">
                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>`;
            }).join('');
        }

        // ── Polling principal ─────────────────────────────────────────────────────

        async function actualizarTickets() {
            // No actualizar si hay un modal abierto (evita interrumpir al usuario)
            if (document.querySelector('#ticketsModalsContainer .modal.show')) return;

            const params = new URLSearchParams(window.location.search);
            try {
                const res = await fetch(`{{ route('admin.tickets.data') }}?${params}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const tickets = await res.json();

                console.log(`[polling] recibidos: ${tickets.length}`);

                // 1. Destruir instancias Bootstrap ANTES de tocar el DOM
                destruirModalesBootstrap();

                // 2. Reemplazar DOM
                renderizarFilas(tickets);
                renderizarModales(tickets);

            } catch (err) {
                console.error('[polling] error:', err);
            }
        }

        // ── Init ──────────────────────────────────────────────────────────────────

        document.addEventListener('DOMContentLoaded', () => {
            actualizarTickets();
            setInterval(actualizarTickets, 6000);

            // quickStore Departamento
            const formDepto = document.getElementById('formCrearDepartamento');
            if (formDepto) {
                formDepto.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const btn = formDepto.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    try {
                        const res = await fetch("{{ route('admin.departamentos.quickStore') }}", {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                            body: new FormData(formDepto)
                        });
                        const data = await res.json();
                        if (!res.ok) { alert(data?.message || 'Error al crear departamento'); return; }

                        const sel = document.getElementById('selectDepartamento');
                        const opt = document.createElement('option');
                        opt.value = data.id_departamento;
                        opt.textContent = data.nombre;
                        opt.selected = true;
                        sel.appendChild(opt);

                        // Actualizar array local para que aparezca en modales dinámicos
                        DEPARTAMENTOS.push({ id_departamento: data.id_departamento, nombre: data.nombre });

                        bootstrap.Modal.getInstance(document.getElementById('modalCrearDepartamento'))?.hide();
                        formDepto.reset();
                        document.getElementById('activoDepto').checked = true;
                    } catch (err) {
                        console.error(err);
                        alert('No se pudo crear el departamento.');
                    } finally {
                        btn.disabled = false;
                    }
                });
            }
        });
    </script>
@endsection
