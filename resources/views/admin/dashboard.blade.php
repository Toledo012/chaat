@extends('layouts.admin')

@section('title', 'Panel de control | SEMAHN')
@section('header_title', 'Panel de Control')
@section('header_subtitle', 'Análisis operativo y actividad reciente')

@section('styles')
    <style>
        .kpi-card { border-radius: 12px; transition: transform 0.2s; }
        .kpi-card:hover { transform: scale(1.02); }
        .kpi-icon-shape { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 10px; }
        .kpi-card h4, .dashboard-metric { font-variant-numeric: tabular-nums; }

        .scroll-container { max-height: 280px; overflow-y: auto; }
        .scroll-container::-webkit-scrollbar { width: 4px; }
        .scroll-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }

        .ticket-feed-item { border-left: 3px solid transparent; transition: background 0.2s; }
        .ticket-feed-item:hover { background-color: var(--surface-muted); }
        .border-alta  { border-left-color: #dc3545 !important; }
        .border-media { border-left-color: #ffc107 !important; }
        .border-baja  { border-left-color: #198754 !important; }
        .dashboard-chart { position: relative; height: 290px; }
        .dashboard-chart-scroll { overflow-x: auto; }
        .dashboard-chart-monthly { min-width: 600px; }
        .dashboard-chart-formats { height: 220px; }
        .dashboard-filters { display: flex; flex-wrap: wrap; gap: .75rem; align-items: end; }
        .dashboard-filters .form-select { min-width: 135px; }
        .material-list { margin: 0; padding: 0; list-style: none; }
        .material-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .8rem 1rem; border-top: 1px solid var(--border-color); }
        .material-name { min-width: 0; overflow-wrap: anywhere; }
        .material-unit { flex: none; background: var(--surface-muted); border: 1px solid var(--border-color); color: var(--secondary-color); border-radius: .5rem; padding: .2rem .6rem; white-space: nowrap; }
        .ticket-feed-item .ticket-line { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .4rem; }
        @media (max-width: 575.98px) {
            .dashboard-chart { height: 250px; }
            .dashboard-chart-formats { height: 210px; }
            .dashboard-filters > div, .dashboard-filters .form-select { width: 100%; }
            .material-row { align-items: flex-start; flex-direction: column; gap: .4rem; }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- ── KPIs ── --}}
        <div class="row mb-3">
            @foreach([
                ['Usuarios',        $stats['total_usuarios'],   'fa-users',          'bg-primary-subtle text-primary'],
                ['Cuentas Activas', $stats['cuentas_activas'],  'fa-user-check',     'bg-success-subtle text-success'],
                ['Servicios',       $stats['total_servicios'],  'fa-clipboard-list', 'bg-info-subtle text-info'],
                ['Tickets Activos',$stats['tickets_abiertos'], 'fa-ticket',         'bg-danger-subtle text-danger'],
            ] as [$label, $value, $icon, $bgClass])
                <div class="col-12 col-sm-6 col-xl-3 mb-3">
                    <div class="card kpi-card border-0 shadow-sm">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-body-secondary text-uppercase fw-bold" style="font-size:.65rem;">{{ $label }}</small>
                                <h4 class="fw-bold mb-0">{{ number_format($value) }}</h4>
                            </div>
                            <div class="kpi-icon-shape {{ $bgClass }}"><i class="fas {{ $icon }}"></i></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @php
            $meses = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h2 class="h6 fw-bold mb-1">Actividad del sistema</h2>
                    <p class="text-body-secondary small mb-0">Tickets por fecha de creación · Formatos por fecha del servicio</p>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="dashboard-filters">
                    <div>
                        <label for="dashboardYear" class="form-label small fw-semibold mb-1">Año</label>
                        <select id="dashboardYear" name="anio" class="form-select form-select-sm">
                            @foreach(range(now()->year, $anioMin) as $opcionAnio)
                                <option value="{{ $opcionAnio }}" @selected($anio === $opcionAnio)>{{ $opcionAnio }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="dashboardMonth" class="form-label small fw-semibold mb-1">Mes para detalle</label>
                        <select id="dashboardMonth" name="mes" class="form-select form-select-sm">
                            <option value="0" @selected($mes === 0)>Todo el año</option>
                            @foreach($meses as $numero => $nombre)
                                <option value="{{ $numero }}" @selected($mes === $numero)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary btn-sm">Aplicar</button>
                </form>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <h2 class="h6 fw-bold mb-1"><i class="fas fa-chart-bar me-2 text-primary"></i>Tickets generados por mes</h2>
                                <p class="small text-body-secondary mb-0">{{ $anio }} frente a {{ $anio - 1 }}{{ $mes ? ' · Detalle: ' . $meses[$mes] : ' · Todo el año' }}</p>
                            </div>
                            <div class="text-md-end">
                                <strong class="dashboard-metric fs-4 d-block lh-1">{{ number_format($mes ? $ticketsMes : $ticketsAnio) }}</strong>
                                <small class="text-body-secondary">tickets en {{ $mes ? strtolower($meses[$mes]) : $anio }}</small>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-3 small text-body-secondary mb-3">
                            @if($mes)
                                <span>{{ ($ticketsMes - $ticketsMesAnterior) >= 0 ? '+' : '' }}{{ $ticketsMes - $ticketsMesAnterior }} frente al mes anterior</span>
                                <span>{{ ($ticketsMes - $ticketsMismoMesAnterior) >= 0 ? '+' : '' }}{{ $ticketsMes - $ticketsMismoMesAnterior }} frente a {{ strtolower($meses[$mes]) }} de {{ $anio - 1 }}</span>
                            @else
                                <span>{{ ($ticketsAnio - $ticketsAnioAnterior) >= 0 ? '+' : '' }}{{ $ticketsAnio - $ticketsAnioAnterior }} frente a {{ $anio - 1 }}</span>
                            @endif
                        </div>
                        @if(array_sum($ticketsPorMes[$anio]) + array_sum($ticketsPorMes[$anio - 1]) > 0)
                            <div class="dashboard-chart-scroll" aria-label="Gráfica desplazable de tickets por mes">
                                <div class="dashboard-chart dashboard-chart-monthly"><canvas id="chartTickets"></canvas></div>
                            </div>
                        @else
                            <p class="text-body-secondary text-center py-5 mb-0">Aún no hay tickets registrados en estos años.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <h2 class="h6 fw-bold mb-1"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Distribución de formatos</h2>
                        <p class="small text-body-secondary mb-3">{{ $mes ? $meses[$mes] . ' de ' . $anio : 'Todo ' . $anio }}</p>
                        @if($totalFormatos > 0)
                            <div class="dashboard-chart dashboard-chart-formats"><canvas id="chartFormatos"></canvas></div>
                            <div class="mt-3 small">
                                @foreach($tiposFormato as $indice => $tipo)
                                    @php $cantidad = (int) ($formatosPorTipo[$tipo] ?? 0); @endphp
                                    <div class="d-flex justify-content-between gap-2 py-1">
                                        <span><span class="d-inline-block rounded-circle me-2" style="width: .65rem; height: .65rem; background: {{ $coloresFormato[$indice] }};"></span>Formato {{ $tipo }}</span>
                                        <span class="fw-semibold">{{ $cantidad }} <span class="text-body-secondary fw-normal">({{ round($cantidad * 100 / $totalFormatos) }}%)</span></span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-body-secondary text-center py-5 mb-0">No hay formatos registrados en este periodo.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">

                    {{-- PRODUCTIVIDAD --}}
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-transparent border-0 pt-3 px-3">
                                <h6 class="fw-bold mb-0 small"><i class="fas fa-users-gear me-2 text-info"></i>Productividad de Equipo</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive scroll-container">
                                    <table class="table table-hover align-middle mb-0" style="font-size:.85rem;">
                                        <thead class="bg-body-tertiary sticky-top">
                                        <tr class="text-body-secondary small">
                                            <th class="ps-3 py-2 border-0">Nombre</th>
                                            <th class="py-2 border-0">Progreso</th>
                                            <th class="text-center py-2 border-0">A/B/C/D</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($usuariosFormatos as $u)
                                            @php $pct = $maxServicios > 0 ? round(($u->total / $maxServicios) * 100) : 0; @endphp
                                            <tr>
                                                <td class="ps-3 fw-bold">{{ $u->nombre }}</td>
                                                <td style="min-width:130px;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-primary rounded-pill px-2">{{ $u->total }}</span>
                                                        <div class="progress flex-grow-1" style="height:5px;">
                                                            <div class="progress-bar bg-primary" style="width:{{ $pct }}%;"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center small fw-bold">
                                                    <span class="text-primary">{{ $u->A }}</span> |
                                                    <span class="text-info">{{ $u->B }}</span> |
                                                    <span class="text-warning">{{ $u->C }}</span> |
                                                    <span class="text-danger">{{ $u->D }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- MINI BANDEJA TICKETS --}}
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-transparent border-0 pt-3 px-3 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 small"><i class="fas fa-bolt me-2 text-danger"></i>Tickets Recientes</h6>
                                <a href="{{ route('admin.tickets.index') }}" class="small text-decoration-none">Ver todos</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush scroll-container">
                                    @forelse($ticketsRecientes as $tr)
                                        <div class="list-group-item ticket-feed-item border-0 border-bottom px-3 py-2 border-{{ $tr->prioridad }}">
                                            <div class="ticket-line mb-1">
                                                <span class="fw-bold text-body small">#{{ $tr->folio }} - {{ \Illuminate\Support\Str::limit($tr->titulo, 38) }}</span>
                                                <small class="text-body-secondary" style="font-size:.65rem;">{{ $tr->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="ticket-line">
                                                <small class="text-body-secondary"><i class="fas fa-user-edit me-1"></i>{{ $tr->solicitante }}</small>
                                                <div class="d-flex gap-1">
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size:.55rem;">
                                                {{ strtoupper($tr->estado) }}
                                            </span>
                                                    <span class="badge rounded-pill {{ $tr->prioridad === 'alta' ? 'bg-danger' : ($tr->prioridad === 'media' ? 'bg-warning text-dark' : 'bg-success') }}" style="font-size:.55rem;">
                                                {{ strtoupper($tr->prioridad) }}
                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-3 text-center text-body-secondary small">Sin actividad reciente.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

        </div>

        {{-- ── FILA FINAL ── --}}
        <div class="row g-4">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center gap-2 px-3 pt-3 pb-2">
                        <h2 class="h6 fw-bold mb-0"><i class="fas fa-box me-2 text-primary"></i>Nuevos materiales</h2>
                        <a href="{{ route('admin.materiales.index') }}" class="small text-decoration-none">Ver catálogo</a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="material-list">
                            @forelse($materiales as $m)
                                <li class="material-row small">
                                    <span class="material-name text-body"><i class="fas fa-tag me-2 text-body-secondary" aria-hidden="true"></i>{{ $m->nombre }}</span>
                                    <span class="material-unit">{{ $m->unidad_sugerida ?: 'Sin unidad' }}</span>
                                </li>
                            @empty
                                <li class="px-3 py-4 text-center text-body-secondary small">Aún no hay materiales en el catálogo.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 p-md-4">
                        <h2 class="h6 fw-bold mb-2"><i class="fas fa-arrow-up-right-from-square me-2 text-primary"></i>Accesos rápidos</h2>
                        <p class="small text-body-secondary mb-3">Continúa con la operación del sistema.</p>
                        <div class="d-grid gap-2">
                            <a class="btn btn-outline-primary text-start" href="{{ route('admin.tickets.index') }}"><i class="fas fa-ticket-alt me-2" aria-hidden="true"></i>Gestionar tickets</a>
                            <a class="btn btn-outline-primary text-start" href="{{ route('admin.formatos.index') }}"><i class="fas fa-file-alt me-2" aria-hidden="true"></i>Consultar formatos</a>
                            <a class="btn btn-outline-primary text-start" href="{{ route('admin.materiales.index') }}"><i class="fas fa-boxes-stacked me-2" aria-hidden="true"></i>Ver materiales</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;

            const palette = () => {
                const styles = getComputedStyle(document.documentElement);
                return {
                    text: styles.getPropertyValue('--bs-secondary-color').trim(),
                    strong: styles.getPropertyValue('--text-color').trim(),
                    border: styles.getPropertyValue('--border-color').trim(),
                };
            };
            const colors = palette();
            const ticketCanvas = document.getElementById('chartTickets');
            const formatCanvas = document.getElementById('chartFormatos');
            const selectedMonth = {{ $mes }};
            let ticketsChart = null;
            let formatsChart = null;

            if (ticketCanvas) {
                ticketsChart = new Chart(ticketCanvas, {
                    type: 'bar',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        datasets: [
                            {
                                label: '{{ $anio }}',
                                data: @json($ticketsActuales),
                                backgroundColor: Array.from({length: 12}, (_, i) => !selectedMonth || i === selectedMonth - 1 ? '#399e91' : 'rgba(57, 158, 145, .38)'),
                                borderRadius: 5,
                                maxBarThickness: 22,
                            },
                            {
                                label: '{{ $anio - 1 }}',
                                data: @json($ticketsAnteriores),
                                backgroundColor: 'rgba(154, 54, 77, .55)',
                                borderRadius: 5,
                                maxBarThickness: 22,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { color: colors.text, usePointStyle: true, boxWidth: 9 } },
                        },
                        scales: {
                            x: { ticks: { color: colors.text }, grid: { display: false } },
                            y: { beginAtZero: true, ticks: { color: colors.text, precision: 0 }, grid: { color: colors.border } },
                        },
                    },
                });
            }

            if (formatCanvas) {
                const total = {{ $totalFormatos }};
                formatsChart = new Chart(formatCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: @json($etiquetasFormato),
                        datasets: [{ data: @json($datosFormato), backgroundColor: @json($coloresFormato), borderWidth: 0, hoverOffset: 6 }],
                    },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '74%', plugins: { legend: { display: false } } },
                    plugins: [{
                        id: 'centerTotal',
                        afterDraw(chart) {
                            const { ctx, chartArea } = chart;
                            ctx.save();
                            ctx.fillStyle = palette().strong;
                            ctx.font = '700 23px Inter, sans-serif';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(total.toLocaleString('es-MX'), (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2);
                            ctx.restore();
                        },
                    }],
                });
            }

            document.addEventListener('themechange', function () {
                const next = palette();
                if (ticketsChart) {
                    ticketsChart.options.plugins.legend.labels.color = next.text;
                    ticketsChart.options.scales.x.ticks.color = next.text;
                    ticketsChart.options.scales.y.ticks.color = next.text;
                    ticketsChart.options.scales.y.grid.color = next.border;
                    ticketsChart.update('none');
                }
                formatsChart?.update('none');
            });
        });
    </script>
@endsection
