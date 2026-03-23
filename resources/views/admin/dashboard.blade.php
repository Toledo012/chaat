@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header_title', 'Panel de Control')
@section('header_subtitle', 'Análisis operativo y actividad reciente')

@section('styles')
    <style>
        .kpi-card { border-radius: 12px; transition: transform 0.2s; }
        .kpi-card:hover { transform: scale(1.02); }
        .kpi-icon-shape { width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 10px; }

        .scroll-container { max-height: 280px; overflow-y: auto; }
        .scroll-container::-webkit-scrollbar { width: 4px; }
        .scroll-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }

        .ticket-feed-item { border-left: 3px solid transparent; transition: background 0.2s; }
        .ticket-feed-item:hover { background-color: #f8fafc; }
        .border-alta  { border-left-color: #dc3545 !important; }
        .border-media { border-left-color: #ffc107 !important; }
        .border-baja  { border-left-color: #198754 !important; }
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
                ['Tickets Abiertos',$stats['tickets_abiertos'], 'fa-ticket',         'bg-danger-subtle text-danger'],
            ] as [$label, $value, $icon, $bgClass])
                <div class="col-6 col-xl-3 mb-3">
                    <div class="card kpi-card border-0 shadow-sm">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">{{ $label }}</small>
                                <h4 class="fw-bold mb-0">{{ number_format($value) }}</h4>
                            </div>
                            <div class="kpi-icon-shape {{ $bgClass }}"><i class="fas {{ $icon }}"></i></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">

            {{-- ── GRÁFICA ── --}}
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 text-center">
                        <h6 class="fw-bold mb-4 text-start"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Distribución</h6>
                        <canvas id="chartFormatos" style="max-height:200px;"></canvas>
                        <div class="mt-4 text-start small">
                            @foreach(['A' => 'primary', 'B' => 'info', 'C' => 'warning', 'D' => 'danger'] as $tipo => $col)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Formato {{ $tipo }}</span>
                                    <span class="badge bg-{{ $col }}-subtle text-{{ $col }} rounded-pill">
                                {{ $formatosPorTipo[$tipo] ?? 0 }}
                            </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── DERECHA ── --}}
            <div class="col-lg-8">
                <div class="row">

                    {{-- PRODUCTIVIDAD --}}
                    <div class="col-12 mb-4">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-transparent border-0 pt-3 px-3">
                                <h6 class="fw-bold mb-0 small"><i class="fas fa-users-gear me-2 text-info"></i>Productividad de Equipo</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive scroll-container">
                                    <table class="table table-hover align-middle mb-0" style="font-size:.85rem;">
                                        <thead class="bg-light sticky-top">
                                        <tr class="text-muted small">
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
                    <div class="col-12 mb-4">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-transparent border-0 pt-3 px-3 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 small"><i class="fas fa-bolt me-2 text-danger"></i>Tickets Recientes</h6>
                                <a href="{{ route('admin.tickets.index') }}" class="small text-decoration-none">Ver todos</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush scroll-container">
                                    @forelse($ticketsRecientes as $tr)
                                        <div class="list-group-item ticket-feed-item border-0 border-bottom px-3 py-2 border-{{ $tr->prioridad }}">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-dark small">#{{ $tr->folio }} - {{ \Illuminate\Support\Str::limit($tr->titulo, 38) }}</span>
                                                <small class="text-muted" style="font-size:.65rem;">{{ $tr->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted"><i class="fas fa-user-edit me-1"></i>{{ $tr->solicitante }}</small>
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
                                        <div class="p-3 text-center text-muted small">Sin actividad reciente.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── FILA FINAL ── --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 p-2 h-100">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="fw-bold mb-0 small"><i class="fas fa-box me-2 text-success"></i>Nuevos Materiales</h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush px-2">
                            @foreach($materiales as $m)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 border-0 border-bottom small">
                                    <span class="text-dark"><i class="fas fa-tag me-2 text-muted"></i>{{ $m->nombre }}</span>
                                    <span class="text-muted">{{ $m->unidad_sugerida }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 text-white d-flex align-items-center justify-content-center"
                     style="background: linear-gradient(135deg, #399e91 0%, #17a2b8 100%);">
                    <div class="text-center p-3">
                        <h5 class="fw-bold mb-3 small">Sistema SEMAHN 2026</h5>
                        <div class="d-flex gap-3 justify-content-center">
                            <div class="bg-white bg-opacity-25 px-3 py-2 rounded-3">
                                <h6 class="mb-0 fw-bold" id="reloj">--:--:--</h6>
                            </div>
                            <div class="bg-white bg-opacity-25 px-3 py-2 rounded-3">
                                <h6 class="mb-0 fw-bold" id="fecha">--/--/----</h6>
                            </div>
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

            // ── Gráfica ──────────────────────────────────────────
            new Chart(document.getElementById('chartFormatos').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Formato A','Formato B','Formato C','Formato D'],
                    datasets: [{
                        data: [
                            {{ $formatosPorTipo['A'] ?? 0 }},
                            {{ $formatosPorTipo['B'] ?? 0 }},
                            {{ $formatosPorTipo['C'] ?? 0 }},
                            {{ $formatosPorTipo['D'] ?? 0 }}
                        ],
                        backgroundColor: ['#399e91','#17a2b8','#ffc107','#dc3545'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '80%',
                    plugins: { legend: { display: false } }
                }
            });

            // ── Reloj en tiempo real ─────────────────────────────
            function tick() {
                const now = new Date();
                document.getElementById('reloj').textContent =
                    now.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                document.getElementById('fecha').textContent =
                    now.toLocaleDateString('es-MX');
            }
            setInterval(tick, 1000);
            tick();
        });
    </script>
@endsection
