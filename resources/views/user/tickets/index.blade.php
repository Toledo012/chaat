@extends('layouts.admin')

@section('title', 'Mis Tickets')
@section('header_title', 'Gestión de Mis Tickets')
@section('header_subtitle', 'Bandeja de atención técnica y seguimiento de solicitudes asignadas')

@section('content')
    <div class="container-fluid">

        {{-- ENCABEZADO --}}
        <div class="d-flex align-items-center gap-3 mb-4 px-2">
            <i class="fas fa-ticket-alt text-primary fa-2x"></i>
            <div>
                <h4 class="mb-0 fw-bold">Tickets</h4>
                <p class="text-muted mb-0 small text-uppercase">Bandeja de trabajo del usuario</p>
            </div>
            <button type="button" class="btn btn-primary ms-auto shadow-sm fw-bold btn-sm px-4 rounded-pill"
                    data-bs-toggle="modal" data-bs-target="#modalCrearTicket">
                <i class="fas fa-plus me-2"></i> Crear Ticket
            </button>
        </div>

        {{-- TICKETS DISPONIBLES --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-inbox me-2 text-primary"></i>Tickets disponibles para atención
                </h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 rounded-pill small" id="badgeDisponibles">
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
                        <tbody id="ticketsDisponiblesBody">
                        @forelse($disponibles as $t)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $t->folio }}</td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $t->titulo }}</div>
                                    <div class="text-muted small"><i class="fas fa-user-edit me-1 small"></i>{{ $t->solicitante }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2">
                                        TIPO {{ strtoupper($t->tipo_formato) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small fw-semibold text-dark">
                                        <i class="fas fa-pen-nib me-1 text-muted small"></i>{{ $t->creador->username ?? 'Sistema' }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <div class="text-nowrap">
                                        <i class="far fa-calendar-plus me-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($t->created_at)->timezone('America/Mexico_City')->format('d/m/Y h:i A') }}
                                    </div>
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
                                <td colspan="6" class="text-center py-4 text-muted small italic">
                                    No hay tickets libres por ahora.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MIS TICKETS --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-user-check me-2 text-primary"></i>Mi bandeja de trabajo
                </h6>
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
                        <tbody id="misTicketsBody">
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
                                            'cancelado'  => 'text-bg-danger',
                                            default      => 'text-bg-info',
                                        };
                                    @endphp
                                    <span class="badge {{ $stClass }} rounded-pill px-3" style="font-size:0.65rem;">
                                        {{ strtoupper(str_replace('_',' ',$t->estado)) }}
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
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#modalGestionUser{{ $t->id_ticket }}">
                                        Gestionar <i class="fas fa-chevron-right ms-1"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted opacity-50 small italic">
                                    No tienes tickets asignados en tu bandeja personal.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Contenedor de modales dinámicos --}}
    <div id="userTicketsModalsContainer"></div>

    {{-- MODAL CREAR TICKET --}}
    <div class="modal fade" id="modalCrearTicket" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered shadow">
            <div class="modal-content border-0">
                <form method="POST" action="{{ route('user.tickets.store') }}">
                    @csrf
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-plus-circle me-2 text-white"></i>Registrar Solicitud
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted text-uppercase">Título *</label>
                                <input type="text" name="titulo" class="form-control shadow-sm border-light-subtle"
                                       required value="{{ old('titulo') }}" placeholder="Ej: Falla en equipo de red">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Solicitante *</label>
                                <input type="text" name="solicitante" class="form-control shadow-sm border-light-subtle"
                                       required value="{{ old('solicitante') }}" placeholder="Nombre del solicitante">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted text-uppercase">Departamento *</label>
                                <div class="input-group">
                                    <select id="selectDepartamentoUser" name="id_departamento" class="form-select shadow-sm border-light-subtle" required>
                                        <option value="">Selecciona un departamento</option>
                                        @foreach($departamentos as $d)
                                            <option value="{{ $d->id_departamento }}" @selected(old('id_departamento') == $d->id_departamento)>
                                                {{ $d->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-success fw-bold"
                                            data-bs-toggle="modal" data-bs-target="#modalCrearDepartamentoUser">
                                        <i class="fas fa-plus me-1"></i> Nuevo
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-1">Si no existe, crea uno aquí mismo.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Tipo de Formato *</label>
                                <select name="tipo_formato" class="form-select shadow-sm border-light-subtle" required>
                                    <option value="">Seleccionar formato...</option>
                                    @foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $k=>$v)
                                        <option value="{{ $k }}" @selected(old('tipo_formato')===$k)>Formato {{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Descripción (Máx 200)</label>
                                <textarea name="descripcion" class="form-control shadow-sm border-light-subtle"
                                          rows="4" maxlength="200"
                                          placeholder="Explique brevemente los detalles del requerimiento...">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>
                        <div class="mt-3 small text-muted text-center border-top pt-3">
                            <i class="fas fa-info-circle me-1"></i> La prioridad de este ticket será establecida por el administrador.
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 text-center">
                        <button type="submit"
                                class="btn btn-primary btn-sm fw-bold px-5 rounded-pill shadow-sm mx-auto"
                                onclick="this.disabled=true; this.innerText='Guardando...'; this.form.submit();">
                            Guardar Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL CREAR DEPARTAMENTO --}}
    <div class="modal fade" id="modalCrearDepartamentoUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-building me-2"></i> Registrar Departamento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrearDepartamentoUser">
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
                            <input class="form-check-input" type="checkbox" value="1" id="activoDeptoUser" name="activo" checked>
                            <label class="form-check-label" for="activoDeptoUser">Activo</label>
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
        const DEPARTAMENTOS_USER = @json($departamentos->map(fn($d) => ['id_departamento' => $d->id_departamento, 'nombre' => $d->nombre])->values());
        const CSRF_USER = '{{ csrf_token() }}';

        // ── Helpers ───────────────────────────────────────────────────────────────

        function escU(v) {
            if (v === null || v === undefined) return '';
            return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
        }

        function fechaMXU(iso) {
            if (!iso) return '';
            const f = new Date(iso);
            return isNaN(f) ? escU(iso) : f.toLocaleDateString('es-MX') + ' ' + f.toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit', hour12:true });
        }

        function estadoBadgeU(estado) {
            const clases = {
                en_proceso: 'text-bg-warning text-dark',
                completado: 'text-bg-success',
                cancelado:  'text-bg-danger',
                asignado:   'text-bg-info',
                nuevo:      'text-bg-primary',
                en_espera:  'text-bg-secondary'
            };
            return `<span class="badge ${clases[estado] ?? 'text-bg-info'} rounded-pill px-3" style="font-size:0.65rem;">
            ${escU(String(estado ?? '').replaceAll('_',' ').toUpperCase())}
        </span>`;
        }

        function opcionesDeptoU(selId) {
            return '<option value="">Selecciona un departamento</option>' +
                DEPARTAMENTOS_USER.map(d =>
                    `<option value="${d.id_departamento}" ${Number(selId) === Number(d.id_departamento) ? 'selected' : ''}>${escU(d.nombre)}</option>`
                ).join('');
        }

        // ── Destruir instancias Bootstrap ANTES de tocar el DOM ───────────────────

        function destruirModalesU() {
            document.querySelectorAll('#userTicketsModalsContainer .modal').forEach(el => {
                const inst = bootstrap.Modal.getInstance(el);
                if (inst) inst.dispose();
            });
        }

        // ── Renderizar tabla disponibles ──────────────────────────────────────────

        function renderDisponibles(disponibles) {
            const tbody = document.getElementById('ticketsDisponiblesBody');
            const badge = document.getElementById('badgeDisponibles');
            if (!tbody) return;

            if (badge) badge.textContent = `${disponibles.length} Disponibles`;

            if (!disponibles.length) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted small italic">No hay tickets libres por ahora.</td></tr>`;
                return;
            }

            tbody.innerHTML = disponibles.map(t => `
            <tr>
                <td class="ps-4 fw-bold text-primary">#${escU(t.folio)}</td>
                <td>
                    <div class="fw-bold text-dark small">${escU(t.titulo)}</div>
                    <div class="text-muted small"><i class="fas fa-user-edit me-1 small"></i>${escU(t.solicitante ?? '')}</div>
                </td>
                <td>
                    <span class="badge bg-secondary-subtle text-secondary border px-2">
                        TIPO ${escU(String(t.tipo_formato ?? '').toUpperCase())}
                    </span>
                </td>
                <td>
                    <span class="small fw-semibold text-dark">
                        <i class="fas fa-pen-nib me-1 text-muted small"></i>${escU(t.creado_por?.username ?? 'Sistema')}
                    </span>
                </td>
                <td class="small text-muted">
                    <div class="text-nowrap">
                        <i class="far fa-calendar-plus me-1 text-primary"></i>${fechaMXU(t.created_at)}
                    </div>
                </td>
                <td class="text-end pe-4">
                    <form method="POST" action="/user/tickets/${t.id_ticket}/tomar">
                        <input type="hidden" name="_token" value="${CSRF_USER}">
                        <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                            Tomar <i class="fas fa-hand-paper ms-1"></i>
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
        }

        // ── Renderizar tabla mis tickets ──────────────────────────────────────────

        function renderMisTickets(misTickets) {
            const tbody = document.getElementById('misTicketsBody');
            if (!tbody) return;

            if (!misTickets.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted opacity-50 small italic">No tienes tickets asignados en tu bandeja personal.</td></tr>`;
                return;
            }

            tbody.innerHTML = misTickets.map(t => {
                const fechaCierre = ['completado','cancelado'].includes(t.estado)
                    ? `<div class="text-success fw-semibold small mt-1"><i class="fas fa-calendar-check me-1 small"></i>${fechaMXU(t.updated_at)}</div>` : '';
                return `
            <tr>
                <td class="ps-4 fw-bold text-primary">#${escU(t.folio)}</td>
                <td>
                    <div class="fw-bold text-dark small">${escU(t.titulo)}</div>
                    <div class="text-muted small">Formato ${escU(String(t.tipo_formato ?? '').toUpperCase())}</div>
                </td>
                <td>${estadoBadgeU(t.estado)}</td>
                <td class="small">
                    <div class="text-muted small"><i class="fas fa-calendar-plus me-1 text-primary small"></i>${fechaMXU(t.created_at)}</div>
                    ${fechaCierre}
                </td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#modalGestionUser${t.id_ticket}">
                        Gestionar <i class="fas fa-chevron-right ms-1"></i>
                    </button>
                </td>
            </tr>`;
            }).join('');
        }

        // ── Renderizar modales dinámicos ──────────────────────────────────────────

        function renderModalesU(misTickets) {
            const contenedor = document.getElementById('userTicketsModalsContainer');
            if (!contenedor) return;

            contenedor.innerHTML = (misTickets || []).map(t => {
                const bloqueado  = ['completado','cancelado'].includes(t.estado);
                const tipo       = String(t.tipo_formato ?? '').toLowerCase();
                const prevUrl    = t.id_servicio ? `/admin/formatos/${tipo}/${t.id_servicio}/preview` : '';
                const pdfUrl     = t.id_servicio ? `/admin/formatos/${tipo}/${t.id_servicio}/pdf` : '';

                return `
            {{-- Modal gestionar --}}
                <div class="modal fade" id="modalGestionUser${t.id_ticket}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered shadow-lg">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-light border-bottom">
                            <h6 class="modal-title fw-bold">Detalles Ticket #${escU(t.folio)}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="mb-4">
                                <h6 class="text-uppercase text-muted small fw-bold mb-1">Título de la Solicitud</h6>
                                <h5 class="fw-bold text-dark">${escU(t.titulo)}</h5>
                                <div class="bg-light p-3 rounded text-muted border small mb-0">
                                    ${escU(t.descripcion ?? 'Sin descripción adicional.')}
                                </div>
                            </div>
                            <div class="row g-3 mb-4 text-start">
                                <div class="col-6">
                                    <small class="text-muted d-block small fw-bold text-uppercase">Solicitante:</small>
                                    <strong class="text-primary small"><i class="fas fa-user me-1 small"></i>${escU(t.solicitante ?? '')}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block small fw-bold text-uppercase">Tipo Formato:</small>
                                    <span class="badge bg-dark">TIPO ${escU(String(t.tipo_formato ?? '').toUpperCase())}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block small fw-bold text-uppercase">Fecha Apertura:</small>
                                    <span class="small fw-semibold text-dark">${fechaMXU(t.created_at)}</span>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block small fw-bold text-uppercase text-end">Fecha Cierre:</small>
                                    <span class="small fw-semibold text-dark">
                                        ${['completado','cancelado'].includes(t.estado) ? fechaMXU(t.updated_at) : 'En proceso'}
                                    </span>
                                </div>
                            </div>
                            <div class="d-grid gap-2 border-top pt-4">
                                ${!bloqueado ? `
                                    <button class="btn btn-warning py-2 fw-bold shadow-sm mb-1"
                                            data-bs-toggle="modal" data-bs-target="#modalEditarTicketUser${t.id_ticket}">
                                        <i class="fas fa-edit me-2"></i> Editar Información
                                    </button>
                                    <a href="/user/tickets/${t.id_ticket}/completar" class="btn btn-primary py-2 fw-bold shadow-sm">
                                        <i class="fas fa-clipboard-check me-2"></i> Completar y Generar Formato
                                    </a>
                                ` : ''}
                                ${(t.estado === 'completado' && t.id_servicio) ? `
                                    <div class="p-3 bg-primary-subtle rounded border border-primary-subtle text-center">
                                        <p class="small fw-bold text-primary mb-2 text-uppercase">Documentación del Servicio</p>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="${prevUrl}" target="_blank" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">
                                                <i class="fas fa-eye me-1"></i> Ver Online
                                            </a>
                                            <a href="${pdfUrl}" target="_blank" class="btn btn-sm btn-danger px-4 fw-bold shadow-sm">
                                                <i class="fas fa-file-pdf me-1"></i> PDF
                                            </a>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal editar --}}
                ${!bloqueado ? `
                <div class="modal fade" id="modalEditarTicketUser${t.id_ticket}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-warning text-dark border-0">
                                <h6 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Editar Ticket #${escU(t.folio)}</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="/user/tickets/${t.id_ticket}">
                                <input type="hidden" name="_token" value="${CSRF_USER}">
                                <input type="hidden" name="_method" value="PUT">
                                <div class="modal-body p-4 text-start">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Título *</label>
                                        <input type="text" name="titulo" class="form-control shadow-sm" required maxlength="255" value="${escU(t.titulo)}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Solicitante *</label>
                                        <input type="text" name="solicitante" class="form-control shadow-sm" required maxlength="150" value="${escU(t.solicitante)}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Departamento *</label>
                                        <select name="id_departamento" class="form-select shadow-sm" required>${opcionesDeptoU(t.id_departamento)}</select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Formato *</label>
                                        <select name="tipo_formato" class="form-select shadow-sm" required>
                                            ${['a','b','c','d'].map(k => `<option value="${k}" ${t.tipo_formato===k?'selected':''}>Formato ${k.toUpperCase()}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Descripción (Máx 200)</label>
                                        <textarea name="descripcion" class="form-control shadow-sm" rows="4" maxlength="200">${escU(t.descripcion ?? '')}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-0">
                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            ` : ''}`;
            }).join('');
        }

        // ── Polling principal ─────────────────────────────────────────────────────

        async function actualizarUser() {
            // No actualizar si hay un modal abierto
            if (document.querySelector('#userTicketsModalsContainer .modal.show')) return;

            try {
                const res = await fetch("{{ route('user.tickets.data') }}", {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (!res.ok) return;

                const data = await res.json();
                console.log(`[polling-user] disponibles: ${data.disponibles?.length} | misTickets: ${data.misTickets?.length}`);

                // 1. Destruir instancias Bootstrap ANTES de tocar el DOM
                destruirModalesU();

                // 2. Reemplazar DOM
                renderDisponibles(data.disponibles ?? []);
                renderMisTickets(data.misTickets ?? []);
                renderModalesU(data.misTickets ?? []);

            } catch (err) {
                console.error('[polling-user] error:', err);
            }
        }

        // ── Init ──────────────────────────────────────────────────────────────────

        document.addEventListener('DOMContentLoaded', () => {
            actualizarUser();
            setInterval(actualizarUser, 6000);

            // quickStore Departamento
            const formDepto = document.getElementById('formCrearDepartamentoUser');
            if (formDepto) {
                formDepto.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const btn = formDepto.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    try {
                        const res = await fetch("{{ route('admin.departamentos.quickStore') }}", {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF_USER, 'Accept': 'application/json' },
                            body: new FormData(formDepto)
                        });
                        const data = await res.json();
                        if (!res.ok) { alert(data?.message || 'Error al crear departamento'); return; }

                        const sel = document.getElementById('selectDepartamentoUser');
                        const opt = document.createElement('option');
                        opt.value = data.id_departamento;
                        opt.textContent = data.nombre;
                        opt.selected = true;
                        sel.appendChild(opt);

                        // Actualizar array local para modales dinámicos
                        DEPARTAMENTOS_USER.push({ id_departamento: data.id_departamento, nombre: data.nombre });

                        bootstrap.Modal.getInstance(document.getElementById('modalCrearDepartamentoUser'))?.hide();
                        formDepto.reset();
                        document.getElementById('activoDeptoUser').checked = true;
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
