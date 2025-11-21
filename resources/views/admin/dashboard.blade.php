@extends('layouts.admin')

{{-- Opcional: Define el título específico de la pestaña y del header --}}
@section('title', 'Admin Dashboard - Principal')
@section('header_title', 'Panel de Administración')
@section('header_subtitle', 'Control total del Sistema de Formatos Digitales')


@section('content')
@php
$materiales = \App\Models\CatalogoMateriales::orderBy('id_material','desc')->limit(5)->get();
$usuarios_activos = \App\Models\Usuario::whereHas('cuenta', fn($q)=>$q->where('estado','activo'))->limit(5)->get();
@endphp


    <div class="row mb-5">
        {{-- Tarjeta: TOTAL USUARIOS --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">TOTAL USUARIOS</div>
                        <div class="stats-number">{{ $stats['total_usuarios'] ?? 0 }}</div>
                    </div>
                    <i class="fas fa-users stats-icon text-primary"></i>
                </div>
            </div>
        </div>
        {{-- Tarjeta: CUENTAS ACTIVAS --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">CUENTAS ACTIVAS</div>
                        <div class="stats-number">{{ $stats['cuentas_activas'] ?? 0 }}</div>
                    </div>
                    <i class="fas fa-user-check stats-icon text-success"></i>
                </div>
            </div>
        </div>
        {{-- Tarjeta: TOTAL SERVICIOS --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">TOTAL SERVICIOS</div>
                        <div class="stats-number">{{ $stats['total_servicios'] ?? 0 }}</div>
                    </div>
                    <i class="fas fa-clipboard-list stats-icon text-info"></i>
                </div>
            </div>
        </div>
        {{-- Tarjeta: FORMATOS --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stats-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">FORMATOS</div>
                        <div class="stats-number">4</div>

                    </div>
                    <i class="fas fa-file-alt stats-icon text-warning"></i>
                </div>
            </div>
        </div>
    </div>  
    {{-- Accesos Rápidos --}}
    
    <div class="row">

    {{-- MATERIALES --}}
    <div class="col-lg-6 mb-4">
        <div class="card card-modern p-3">
            <h5 class="fw-bold mb-3">
                <i class="fa-solid fa-box me-2 text-success"></i> Últimos materiales añadidos
            </h5>

            @if($materiales->isEmpty())
                <p class="text-muted">No hay materiales registrados.</p>
            @else
                <ul class="list-group mb-3">
                    @foreach($materiales as $m)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $m->nombre }}</span>
                            <span class="badge bg-secondary">{{ $m->unidad_sugerida }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <a href="{{ route('admin.materiales.index') }}" class="btn btn-sem w-100">
                <i class="fa-solid fa-arrow-right"></i> Ir a Materiales
            </a>
        </div>
    </div>

    {{-- USUARIOS ACTIVOS --}}
    <div class="col-lg-6 mb-4">
        <div class="card card-modern p-3">
            <h5 class="fw-bold mb-3">
                <i class="fa-solid fa-user-check me-2 text-info"></i> Usuarios activos
            </h5>

            @if($usuarios_activos->isEmpty())
                <p class="text-muted">No hay usuarios activos.</p>
            @else
                <ul class="list-group mb-3">
                    @foreach($usuarios_activos as $u)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $u->nombre }}</span>
                            <span class="badge bg-success">Activo</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <a href="{{ route('admin.users.index') }}" class="btn btn-sem w-100">
                <i class="fa-solid fa-arrow-right"></i> Ver Usuarios
            </a>
        </div>
    </div>

</div>

@endsection




{{-- 🔥 Agrega el script de animación aquí para que se ejecute DESPUÉS de los scripts principales --}}
@section('scripts')
    <script>
        // Este script usa las clases 'loading' y 'loaded' que tienes en tu CSS
        const elements = document.querySelectorAll('.stats-card, .quick-actions .btn');
        elements.forEach((element, index) => {
            element.classList.add('loading');
            setTimeout(() => {
                element.classList.add('loaded');
            }, index * 100);
        });
        
        // Efecto hover mejorado que también tenías en el original
        const cards = document.querySelectorAll('.stats-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
@endsection