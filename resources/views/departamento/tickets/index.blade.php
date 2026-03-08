@extends('layouts.departamento')

@section('title', 'Tickets Departamento')
@section('header_title', 'Mis Solicitudes')
@section('header_subtitle', 'Seguimiento de tickets enviados al área de sistemas')

@section('content')
    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="d-flex align-items-center gap-3 mb-4 px-2">
            <i class="fas fa-ticket-alt text-primary fa-2x"></i>
            <div>
                <h4 class="mb-0 fw-bold">Tickets</h4>
                <p class="text-muted mb-0 small text-uppercase">Bandeja de mi departamento</p>
            </div>
            <button type="button" class="btn btn-primary ms-auto shadow-sm fw-bold btn-sm px-4 rounded-pill"
                    data-bs-toggle="modal" data-bs-target="#modalCrearTicket">
                <i class="fas fa-plus me-2"></i> Nuevo Ticket
            </button>
        </div>

        {{-- FILTROS --}}
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

        {{-- TABLA --}}
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
                        <tbody id="ticketsDeptoBody">
                        @forelse($tickets as $t)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">#{{ $t->folio }}</td>
                                <td>
                                    <div class="fw-bold text-dark small text-truncate" style="max-width:250px;">{{ $t->titulo }}</div>
                                    <div class="text-muted small"><i class="fas fa-user-edit me-1"></i>{{ $t->solicitante }}</div>
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
                                    <small class="{{ $prioColor }} fw-bold text-uppercase" style="font-size:0.6rem;">
                                        <i class="fas fa-flag me-1"></i>{{ $t->prioridad }}
                                    </small>
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
                                            data-bs-toggle="modal" data-bs-target="#modalDetalle{{ $t->id_ticket }}">
                                        Gestionar <i class="fas fa-chevron-right ms-1"></i>
                                    </button>
                                </td>
                            </tr>
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

            {{-- Contenedor de modales dinámicos --}}
            <div id="ticketsDeptoModalsContainer"></div>

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
                            <input type="text" name="titulo" class="form-control shadow-sm border-light-subtle" required maxlength="255"
                                   placeholder="Ej: Problemas con el correo institucional">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Solicitante *</label>
                            <input type="text" name="solicitante" class="form-control shadow-sm border-light-subtle" required maxlength="150"
                                   value="{{ Auth::user()->nombre }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted text-uppercase">Descripción del problema (Máx. 200 caracteres) *</label>
                            <textarea name="descripcion" class="form-control shadow-sm border-light-subtle" rows="4" required maxlength="200"
                                      placeholder="Describa brevemente lo ocurrido..."></textarea>
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
                new bootstrap.Modal(document.getElementById('modalCrearTicket')).show();
            });
        </script>
    @endif

@endsection

@section('scripts')
    <script>
        const CSRF_DEPTO = '{{ csrf_token() }}';

        // ── Helpers ───────────────────────────────────────────────────────────────

        function escD(v) {
            if (v === null || v === undefined) return '';
            return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
        }

        function fechaMXD(iso) {
            if (!iso) return '';
            const f = new Date(iso);
            return isNaN(f) ? escD(iso) : f.toLocaleDateString('es-MX') + ' ' + f.toLocaleTimeString('es-MX', { hour:'2-digit', minute:'2-digit', hour12:true });
        }

        function estadoClassD(e) {
            return {
                nuevo:      'text-bg-primary',
                asignado:   'text-bg-info',
                en_proceso: 'text-bg-warning text-dark',
                completado: 'text-bg-success',
                cancelado:  'text-bg-danger',
                en_espera:  'text-bg-dark'
            }[e] ?? 'text-bg-dark';
        }

        function prioClassD(p) {
            return { alta:'text-danger', media:'text-warning', baja:'text-success' }[p] ?? 'text-success';
        }

        // Editar solo está permitido cuando el ticket está en estado "nuevo"
        function puedeEditar(estado) {
            return estado === 'nuevo';
        }

        // ── Destruir instancias Bootstrap ANTES de tocar el DOM ───────────────────

        function destruirModalesD() {
            document.querySelectorAll('#ticketsDeptoModalsContainer .modal').forEach(el => {
                const inst = bootstrap.Modal.getInstance(el);
                if (inst) inst.dispose();
            });
        }

        // ── Renderizar tabla ──────────────────────────────────────────────────────

        function renderTablaD(tickets) {
            const tbody = document.getElementById('ticketsDeptoBody');
            if (!tbody) return;

            if (!Array.isArray(tickets) || !tickets.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5">
                <div class="text-muted opacity-50 small italic">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i> No has enviado solicitudes recientemente.
                </div></td></tr>`;
                return;
            }

            tbody.innerHTML = tickets.map(t => {
                const fechaCierre = ['completado','cancelado'].includes(t.estado)
                    ? `<div class="text-success fw-semibold small mt-1"><i class="fas fa-calendar-check me-1 small"></i>${fechaMXD(t.updated_at)}</div>` : '';
                return `
            <tr>
                <td class="ps-4 fw-bold text-primary">#${escD(t.folio)}</td>
                <td>
                    <div class="fw-bold text-dark small text-truncate" style="max-width:250px;">${escD(t.titulo)}</div>
                    <div class="text-muted small"><i class="fas fa-user-edit me-1"></i>${escD(t.solicitante)}</div>
                </td>
                <td class="text-center">
                    <span class="badge ${estadoClassD(t.estado)} rounded-pill px-3 mb-1 small d-block mx-auto" style="width:fit-content;">
                        ${escD(String(t.estado).replaceAll('_',' '))}
                    </span>
                    <small class="${prioClassD(t.prioridad)} fw-bold text-uppercase" style="font-size:0.6rem;">
                        <i class="fas fa-flag me-1"></i>${escD(t.prioridad ?? '')}
                    </small>
                </td>
                <td class="small">
                    <div class="text-muted small"><i class="fas fa-calendar-plus me-1 text-primary small"></i>${fechaMXD(t.created_at)}</div>
                    ${fechaCierre}
                </td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm"
                            data-bs-toggle="modal" data-bs-target="#modalDetalle${t.id_ticket}">
                        Gestionar <i class="fas fa-chevron-right ms-1"></i>
                    </button>
                </td>
            </tr>`;
            }).join('');
        }

        // ── Renderizar modales ────────────────────────────────────────────────────

        function renderModalesD(tickets) {
            const contenedor = document.getElementById('ticketsDeptoModalsContainer');
            if (!contenedor) return;

            contenedor.innerHTML = tickets.map(t => {
                const bloqueado  = ['completado','cancelado'].includes(t.estado);
                const editable   = puedeEditar(t.estado); // solo "nuevo"
                const tipo       = String(t.tipo_formato ?? '').toLowerCase();
                const pdfUrl     = t.id_servicio ? `/admin/formatos/${tipo}/${t.id_servicio}/pdf` : '';

                return `
            {{-- Modal gestionar --}}
                <div class="modal fade" id="modalDetalle${t.id_ticket}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered shadow-lg">
                    <div class="modal-content border-0">
                        <div class="modal-header bg-light border-bottom">
                            <h6 class="modal-title fw-bold">Ticket #${escD(t.folio)}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="mb-4">
                                <h6 class="text-uppercase text-muted small fw-bold mb-1">Detalles de la Solicitud</h6>
                                <h5 class="fw-bold text-dark">${escD(t.titulo)}</h5>
                                <div class="bg-light p-3 rounded text-muted border small mb-0">
                                    ${escD(t.descripcion ?? 'Sin descripción adicional.')}
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <small class="text-muted d-block small fw-bold text-uppercase">Solicitante:</small>
                                    <strong class="text-primary small"><i class="fas fa-user me-1 small"></i>${escD(t.solicitante)}</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block small fw-bold text-uppercase">Estado:</small>
                                    <span class="badge ${estadoClassD(t.estado)} px-3 rounded-pill">
                                        ${escD(String(t.estado).toUpperCase())}
                                    </span>
                                </div>
                            </div>
                            <div class="d-grid gap-2 border-top pt-4">

                                {{-- Botón editar: solo visible si estado === "nuevo" --}}
                ${editable ? `
                                    <button class="btn btn-warning py-2 fw-bold shadow-sm mb-2"
                                            data-bs-toggle="modal" data-bs-target="#modalEditarTicket${t.id_ticket}">
                                        <i class="fas fa-edit me-2"></i> Editar Información
                                    </button>
                                ` : `
                                    <button class="btn btn-warning py-2 fw-bold shadow-sm mb-2" disabled title="No se puede editar en estado '${escD(t.estado)}'">
                                        <i class="fas fa-lock me-2"></i> Editar Información
                                        <small class="d-block" style="font-size:0.65rem; opacity:0.8;">Solo disponible en estado Nuevo</small>
                                    </button>
                                `}

                                ${(t.estado === 'completado' && t.id_servicio) ? `
                                    <p class="small fw-bold text-primary mb-2 text-uppercase text-center">Formato Finalizado</p>
                                    <div class="d-flex gap-2 justify-content-center mb-3">
                                        <a href="${pdfUrl}" target="_blank" class="btn btn-sm btn-danger px-4 fw-bold shadow-sm">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                    </div>
                                ` : ''}

                                <form method="POST" action="/departamento/tickets/${t.id_ticket}/cancelar">
                                    <input type="hidden" name="_token" value="${CSRF_DEPTO}">
                                    <button class="btn btn-outline-danger btn-sm w-100 fw-bold border-2"
                                            onclick="return confirm('¿Confirmar cancelación de la solicitud?')"
                                            ${bloqueado ? 'disabled' : ''}>
                                        <i class="fas fa-ban me-1"></i> Cancelar Solicitud
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal editar: solo si estado === "nuevo" --}}
                ${editable ? `
                <div class="modal fade" id="modalEditarTicket${t.id_ticket}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-warning text-dark border-0">
                                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Editar Ticket #${escD(t.folio)}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="/departamento/tickets/${t.id_ticket}">
                                <input type="hidden" name="_token" value="${CSRF_DEPTO}">
                                <input type="hidden" name="_method" value="PUT">
                                <div class="modal-body p-4 text-start">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Título de la Solicitud *</label>
                                        <input type="text" name="titulo" class="form-control shadow-sm" required maxlength="255" value="${escD(t.titulo)}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Nombre del Solicitante *</label>
                                        <input type="text" name="solicitante" class="form-control shadow-sm" required maxlength="150" value="${escD(t.solicitante)}">
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Descripción (Máx. 200 caracteres)</label>
                                        <textarea name="descripcion" class="form-control shadow-sm" rows="4" maxlength="200">${escD(t.descripcion ?? '')}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-0">
                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill"
                                            data-bs-toggle="modal" data-bs-target="#modalDetalle${t.id_ticket}">
                                        Volver
                                    </button>
                                    <button type="submit" class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm">
                                        Actualizar Ticket
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            ` : ''}`;
            }).join('');
        }

        // ── Polling principal ─────────────────────────────────────────────────────

        async function actualizarDepto() {
            // No actualizar si hay un modal abierto
            if (document.querySelector('#ticketsDeptoModalsContainer .modal.show')) return;

            const params = new URLSearchParams(window.location.search);
            try {
                const res = await fetch(`{{ route('departamento.tickets.data') }}?${params}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (!res.ok) return;

                const tickets = await res.json();
                console.log(`[polling-depto] recibidos: ${tickets.length}`);

                // 1. Destruir instancias Bootstrap ANTES de tocar el DOM
                destruirModalesD();

                // 2. Reemplazar DOM
                renderTablaD(tickets);
                renderModalesD(tickets);

            } catch (err) {
                console.error('[polling-depto] error:', err);
            }
        }

        // ── Init ──────────────────────────────────────────────────────────────────

        document.addEventListener('DOMContentLoaded', () => {
            actualizarDepto();
            actualizarDepto();
            setInterval(actualizarDepto, 6000);
        });
    </script>
@endsection
