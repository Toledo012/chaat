@extends('layouts.departamento')

@section('title', 'Mis solicitudes | SEMAHN')
@section('header_title', 'Mis solicitudes')
@section('header_subtitle', 'Seguimiento de tickets del área')

@section('styles')
<style>
    .department-welcome { background: var(--brand-action); color: #fff; border-radius: 1rem; }
    .department-welcome .btn { color: var(--brand-action); background: #fff; border-color: #fff; }
    .department-kpi, .department-card { border-radius: 1rem; }
    .department-kpi h3, .department-highlight { font-variant-numeric: tabular-nums; }
    .department-icon { width: 44px; height: 44px; flex: none; display: grid; place-items: center; border-radius: .75rem; }
    .dashboard-period-filter { display: flex; flex-wrap: wrap; align-items: end; gap: .75rem; }
    .dashboard-period-filter .form-select { min-width: 135px; }
    .department-chart { position: relative; height: 280px; }
    .department-chart-scroll { overflow-x: auto; }
    .department-chart-monthly { min-width: 600px; }
    .department-chart-distribution { height: 215px; }
    .department-legend-row { display: flex; justify-content: space-between; gap: .75rem; padding: .25rem 0; }
    .department-request { padding: .8rem 1rem; border-top: 1px solid var(--border-color); }
    .department-request-top { display: flex; justify-content: space-between; flex-wrap: wrap; gap: .4rem; }
    .department-request-title { min-width: 0; overflow-wrap: anywhere; }
    .department-profile-row { display: flex; justify-content: space-between; flex-wrap: wrap; gap: .35rem 1rem; overflow-wrap: anywhere; }
    @media (max-width: 575.98px) {
        .dashboard-period-filter > div, .dashboard-period-filter .form-select { width: 100%; }
        .department-chart { height: 245px; }
        .department-chart-distribution { height: 205px; }
    }
</style>
@endsection

@section('content')
@php
    $account = Auth::user();
    $name = $account->usuario?->nombre ?? $account->username;
    $total = (int) $allStatusCounts->sum();
    $active = $total - (int) ($allStatusCounts['completado'] ?? 0) - (int) ($allStatusCounts['cancelado'] ?? 0);
    $completed = (int) ($allStatusCounts['completado'] ?? 0);
    $waiting = (int) ($allStatusCounts['en_espera'] ?? 0);
    $selectedTickets = $month ? $ticketActivity['current'][$month - 1] : array_sum($ticketActivity['current']);
    $comparisonTickets = $month ? $ticketActivity['previous'][$month - 1] : array_sum($ticketActivity['previous']);
    $statusLabels = ['nuevo' => 'Nuevo', 'asignado' => 'Asignado', 'en_proceso' => 'En proceso', 'en_espera' => 'En espera', 'completado' => 'Completado', 'cancelado' => 'Cancelado'];
    $statusPalette = ['nuevo' => '#65afbd', 'asignado' => '#399e91', 'en_proceso' => '#c8a457', 'en_espera' => '#9a7a57', 'completado' => '#5c9b78', 'cancelado' => '#9a364d'];
    $statusTypes = array_values(array_filter(array_keys($statusLabels), fn ($type) => ($statusCounts[$type] ?? 0) > 0));
    $chartLabels = array_map(fn ($type) => $statusLabels[$type], $statusTypes);
    $chartValues = array_map(fn ($type) => (int) $statusCounts[$type], $statusTypes);
    $chartColors = array_map(fn ($type) => $statusPalette[$type], $statusTypes);
@endphp

<div class="container-fluid px-2">
    <div class="card department-welcome border-0 mb-4">
        <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Hola, {{ explode(' ', trim($name))[0] }}</h2>
                <p class="mb-0 small opacity-75">{{ $account->usuario?->departamentos?->nombre ?? 'Tu área' }} · Consulta el avance de tus solicitudes</p>
            </div>
            <a href="{{ route('departamento.tickets.index') }}" class="btn btn-sm fw-semibold"><i class="fas fa-plus-circle me-2" aria-hidden="true"></i>Nueva solicitud</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Enviadas', $total, 'fa-layer-group', 'bg-primary-subtle text-primary'],
            ['Activas', $active, 'fa-spinner', 'bg-warning-subtle text-warning'],
            ['En espera', $waiting, 'fa-clock', 'bg-info-subtle text-info'],
            ['Resueltas', $completed, 'fa-check-double', 'bg-success-subtle text-success'],
        ] as [$label, $value, $icon, $color])
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card department-kpi border-0 shadow-sm h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between gap-2">
                        <div><small class="text-body-secondary text-uppercase fw-bold" style="font-size:.65rem;">{{ $label }}</small><h3 class="h4 fw-bold mb-0">{{ number_format($value) }}</h3></div>
                        <span class="department-icon {{ $color }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card department-card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div><h2 class="h6 fw-bold mb-1">Actividad de mis solicitudes</h2><p class="small text-body-secondary mb-0">Solo tickets creados por tu cuenta</p></div>
            @include('partials.dashboard-period-filter', ['action' => route('departamento.dashboard'), 'yearMin' => $ticketActivity['yearMin']])
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card department-card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                        <div><h2 class="h6 fw-bold mb-1"><i class="fas fa-chart-bar me-2 text-primary"></i>Solicitudes por mes</h2><p class="small text-body-secondary mb-0">{{ $year }} frente a {{ $year - 1 }}{{ $month ? ' · Detalle: ' . $months[$month] : ' · Todo el año' }}</p></div>
                        <div class="text-md-end"><strong class="department-highlight fs-4 d-block lh-1">{{ number_format($selectedTickets) }}</strong><small class="text-body-secondary">{{ $month ? 'en ' . strtolower($months[$month]) : 'en ' . $year }}</small></div>
                    </div>
                    <p class="small text-body-secondary mb-3">{{ $selectedTickets - $comparisonTickets >= 0 ? '+' : '' }}{{ $selectedTickets - $comparisonTickets }} frente al mismo {{ $month ? 'mes' : 'periodo' }} de {{ $year - 1 }}</p>
                    @if(array_sum($ticketActivity['current']) + array_sum($ticketActivity['previous']) > 0)
                        <div class="department-chart-scroll" aria-label="Gráfica desplazable de solicitudes por mes">
                            <div class="department-chart department-chart-monthly"><canvas data-ticket-activity data-current='@json($ticketActivity['current'])' data-previous='@json($ticketActivity['previous'])' data-year="{{ $year }}" data-month="{{ $month }}"></canvas></div>
                        </div>
                    @else
                        <p class="text-body-secondary text-center py-5 mb-0">Aún no hay solicitudes en estos años.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card department-card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <h2 class="h6 fw-bold mb-1"><i class="fas fa-chart-pie me-2 text-primary"></i>Estados de mis solicitudes</h2>
                    <p class="small text-body-secondary mb-3">{{ $month ? $months[$month] . ' de ' . $year : 'Todo ' . $year }}</p>
                    @if($statusCounts->sum() > 0)
                        <div class="department-chart department-chart-distribution"><canvas data-role-distribution data-labels='@json($chartLabels)' data-values='@json($chartValues)' data-colors='@json($chartColors)'></canvas></div>
                        <div class="mt-3 small">
                            @foreach($statusTypes as $index => $type)
                                <div class="department-legend-row"><span><span class="d-inline-block rounded-circle me-2" style="width:.65rem;height:.65rem;background:{{ $chartColors[$index] }};"></span>{{ $statusLabels[$type] }}</span><span class="fw-semibold">{{ $chartValues[$index] }} <span class="text-body-secondary fw-normal">({{ round($chartValues[$index] * 100 / $statusCounts->sum()) }}%)</span></span></div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-body-secondary text-center py-5 mb-0">No hay solicitudes en este periodo.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card department-card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-header bg-transparent border-0 px-3 px-md-4 pt-3 pt-md-4 pb-2 d-flex flex-wrap justify-content-between align-items-center gap-2"><h2 class="h6 fw-bold mb-0"><i class="fas fa-list-ul me-2 text-primary"></i>Solicitudes recientes</h2><a href="{{ route('departamento.tickets.index') }}" class="small text-decoration-none">Ver todas</a></div>
                <div class="card-body p-0">
                    @forelse($misSolicitudes as $ticket)
                        @php $color = match($ticket->estado) { 'nuevo' => 'primary', 'completado' => 'success', 'cancelado' => 'danger', default => 'warning' }; @endphp
                        <div class="department-request">
                            <div class="department-request-top"><span class="department-request-title small fw-semibold">#{{ $ticket->folio }} · {{ \Illuminate\Support\Str::limit($ticket->titulo, 70) }}</span><span class="badge bg-{{ $color }}-subtle text-{{ $color }} rounded-pill">{{ str_replace('_', ' ', ucfirst($ticket->estado)) }}</span></div>
                            <small class="text-body-secondary">Registrada {{ $ticket->created_at->format('d/m/Y') }}</small>
                        </div>
                    @empty
                        <p class="text-body-secondary text-center py-5 mb-0">Aún no has enviado solicitudes.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card department-card border-0 shadow-sm h-100"><div class="card-body p-3 p-md-4">
                <h2 class="h6 fw-bold mb-3"><i class="fas fa-user me-2 text-primary"></i>Mi perfil</h2>
                <strong class="d-block">{{ $name }}</strong><small class="text-body-secondary">{{ $account->usuario?->puesto ?? 'Departamento' }}</small>
                <div class="border-top mt-3 pt-3 small"><div class="department-profile-row mb-2"><span class="text-body-secondary">Área</span><span class="fw-semibold">{{ $account->usuario?->departamentos?->nombre ?? 'Sin departamento' }}</span></div><div class="department-profile-row"><span class="text-body-secondary">Usuario</span><span class="fw-semibold">{{ $account->username }}</span></div></div>
                <a href="{{ route('departamento.tickets.index') }}" class="btn btn-outline-primary btn-sm w-100 mt-4">Ir a mis tickets</a>
            </div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/role-dashboard.js') }}"></script>
@endsection
