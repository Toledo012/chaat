@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header_title', 'Panel de Administración')
@section('header_subtitle', 'Resumen general del sistema')

@section('content')
@php
use Illuminate\Support\Facades\DB;

$materiales = \App\Models\CatalogoMateriales::orderBy('id_material','desc')->limit(5)->get();

/* Datos para gráfica */
$formatosPorTipo = DB::table('servicios')
    ->select('tipo_formato', DB::raw('COUNT(*) as total'))
    ->groupBy('tipo_formato')
    ->pluck('total','tipo_formato');

/* TODOS los usuarios con conteo */
$usuariosFormatos = DB::table('usuarios')
    ->leftJoin('servicios','usuarios.id_usuario','=','servicios.id_usuario')
    ->select(
        'usuarios.nombre',
        DB::raw('COUNT(servicios.id_servicio) as total'),
        DB::raw("SUM(servicios.tipo_formato='A') as A"),
        DB::raw("SUM(servicios.tipo_formato='B') as B"),
        DB::raw("SUM(servicios.tipo_formato='C') as C"),
        DB::raw("SUM(servicios.tipo_formato='D') as D")
    )
    ->groupBy('usuarios.nombre')
    ->orderByDesc('total')
    ->get();
@endphp

{{-- ================= KPIs ================= --}}
<div class="row mb-4">  
    @foreach([
        ['TOTAL USUARIOS', $stats['total_usuarios'] ?? 0, 'users','primary'],
        ['CUENTAS ACTIVAS', $stats['cuentas_activas'] ?? 0, 'user-check','success'],
        ['TOTAL SERVICIOS', $stats['total_servicios'] ?? 0, 'clipboard-list','info'],
        ['FORMATOS', 4, 'file-alt','warning'],
    ] as [$label,$value,$icon,$color])
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stats-card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-label">{{ $label }}</div>
                    <div class="stats-number">{{ $value }}</div>
                </div>
                <i class="fas fa-{{ $icon }} stats-icon text-{{ $color }}"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ================= GRÁFICA + USUARIOS ================= --}}
<div class="row mb-4">

    {{-- GRÁFICA PASTEL --}}
    <div class="col-lg-4">
        <div class="card card-modern p-3 h-100">
            <h5 class="fw-bold mb-3">
                <i class="fa-solid fa-chart-pie me-2 text-primary"></i>
                Formatos por tipo
            </h5>

            <div class="d-flex justify-content-center">
                <canvas id="chartFormatos" style="max-width: 360px; max-height: 360px;"></canvas>
            </div>
        </div>
    </div>

    {{-- USUARIOS --}}
    <div class="col-lg-8">
        <div class="card card-modern p-3 h-100">
            <h5 class="fw-bold mb-3">
                <i class="fa-solid fa-users me-2 text-info"></i>
                Usuarios y formatos generados
            </h5>

            <div class="table-responsive" style="max-height: 360px; overflow-y:auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Total</th>
                            <th>A</th>
                            <th>B</th>
                            <th>C</th>
                            <th>D</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuariosFormatos as $u)
                        <tr>
                            <td>{{ $u->nombre }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $u->total }}
                                </span>
                            </td>
                            <td>{{ $u->A }}</td>
                            <td>{{ $u->B }}</td>
                            <td>{{ $u->C }}</td>
                            <td>{{ $u->D }}</td>
                        </tr>
                        @endforeach
                    </tbody>

                    
                </table>
                                    <a href="{{ route('admin.users.index') }}"
               class="btn btn-sem w-100 mt-3">
                Ver Usuarios
            </a>
            </div>
        </div>


    </div>

</div>

{{-- ================= MATERIALES ================= --}}
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card card-modern p-3">
            <h5 class="fw-bold mb-3">
                <i class="fa-solid fa-box me-2 text-success"></i>
                Últimos materiales
            </h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th class="text-end">Unidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materiales as $m)
                        <tr>
                            <td>{{ $m->nombre }}</td>
                            <td class="text-end">
                                <span class="badge bg-info">
                                    {{ $m->unidad_sugerida }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <a href="{{ route('admin.materiales.index') }}"
               class="btn btn-sem w-100 mt-3">
                Ver materiales
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('chartFormatos');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Formato A','Formato B','Formato C','Formato D'],
        datasets: [{
            data: [
                {{ $formatosPorTipo['A'] ?? 0 }},
                {{ $formatosPorTipo['B'] ?? 0 }},
                {{ $formatosPorTipo['C'] ?? 0 }},
                {{ $formatosPorTipo['D'] ?? 0 }}
            ],
            backgroundColor: [
                '#399e91',
                '#17a2b8',
                '#ffc107',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: document.body.classList.contains('dark-mode')
                        ? '#e9ecef'
                        : '#343a40'
                }
            }
        }
    }
});
</script>
@endsection
