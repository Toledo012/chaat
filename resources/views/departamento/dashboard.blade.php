@extends('layouts.departamento')

@section('title', 'Dashboard Departamento')

@section('styles')
<style>
    .kpi-card { border-radius: 15px; transition: transform 0.2s; border: none; }
    .kpi-card:hover { transform: translateY(-3px); }
    .icon-shape { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
    
    .ticket-row { transition: background 0.2s; }
    .ticket-row:hover { background-color: #f8fafc; }
    
    .welcome-card {
        background: linear-gradient(135deg, #399e91 0%, #2c7a70 100%);
        border-radius: 15px;
        color: white;
    }
</style>
@endsection

@section('content')
@php
    $user = Auth::user();
    // Obtenemos los tickets creados por esta cuenta 
    $misSolicitudes = \App\Models\Ticket::where('creado_por', $user->id_cuenta)
        ->orderBy('created_at', 'desc')
        ->get();

    $stats = [
        'total' => $misSolicitudes->count(),
        'pendientes' => $misSolicitudes->whereIn('estado', ['nuevo', 'asignado', 'en_proceso'])->count(),
        'completados' => $misSolicitudes->where('estado', 'completado')->count(),
    ];
@endphp

<div class="container-fluid px-2">
    
    {{-- 🏷️ BIENVENIDA Y ACCIÓN RÁPIDA --}}
    <div class="card welcome-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8 text-center text-md-start">
                    <h3 class="fw-bold mb-1">¡Hola, {{ explode(' ', $user->usuario->nombre)[0] }}!</h3>
                    <p class="opacity-75 mb-0 small text-uppercase">Área: {{ $user->usuario->departamentos->nombre ?? 'Sin Departamento' }}</p>
                </div>
                <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                    <a href="{{ route('departamento.tickets.index') }}" class="btn btn-white fw-bold px-4 rounded-pill shadow-sm" style="background: white; color: #399e91;">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Solicitud
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 KPIs DE MI DEPARTAMENTO --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-shape bg-primary-subtle text-primary me-3">
                        <i class="fas fa-layer-group fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Total Enviados</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-shape bg-warning-subtle text-warning me-3">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">En Seguimiento</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $stats['pendientes'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-shape bg-success-subtle text-success me-3">
                        <i class="fas fa-check-double fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Resueltos</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ $stats['completados'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 🔷 COLUMNA IZQUIERDA: ÚLTIMAS SOLICITUDES --}}
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="fas fa-list-ul me-2 text-primary"></i>Estatus de mis solicitudes</h6>
                    <a href="{{ route('departamento.tickets.index') }}" class="small text-decoration-none">Ver todas</a>
                </div>
                <div class="card-body px-0 py-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-muted">
                                <tr>
                                    <th class="ps-4 border-0">Folio</th>
                                    <th class="border-0">Título</th>
                                    <th class="text-center border-0">Estado</th>
                                    <th class="text-end pe-4 border-0">Registro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($misSolicitudes->take(5) as $ticket)
                                <tr class="ticket-row">
                                    <td class="ps-4 fw-bold text-primary small">#{{ $ticket->folio }}</td>
                                    <td class="small fw-semibold text-dark">{{ \Illuminate\Support\Str::limit($ticket->titulo, 45) }}</td>
                                    <td class="text-center">
                                        @php
                                            $color = match($ticket->estado) {
                                                'nuevo' => 'primary',
                                                'completado' => 'success',
                                                'cancelado' => 'danger',
                                                default => 'warning'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $color }}-subtle text-{{ $color }} rounded-pill px-3" style="font-size: 0.6rem;">
                                            {{ strtoupper($ticket->estado) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 small text-muted">{{ $ticket->created_at->format('d/m/y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted small italic">No hay solicitudes recientes.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔷 COLUMNA DERECHA: PERFIL Y SEGURIDAD --}}
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                <h6 class="fw-bold mb-3 text-muted text-uppercase small" style="letter-spacing: 1px;">Mi Perfil</h6>
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-light p-3 rounded-circle me-3 text-primary shadow-sm">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">{{ $user->usuario->nombre }}</h6>
                        <small class="text-muted">{{ $user->usuario->puesto }}</small>
                    </div>
                </div>

                <div class="bg-light p-3 rounded-3 mb-4">
                    <div class="small mb-2">
                        <span class="text-muted">Username:</span> <span class="fw-bold text-dark">{{ $user->username }}</span>
                    </div>
                    <div class="small">
                        <span class="text-muted">Estado:</span> <span class="badge bg-success px-2 rounded-pill">Activo</span>
                    </div>
                </div>

                <button class="btn btn-outline-primary w-100 fw-bold rounded-pill btn-sm shadow-sm mt-auto" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="fas fa-key me-2"></i>Cambiar Contraseña
                </button>
            </div>
        </div>
    </div>
</div>
@endsection