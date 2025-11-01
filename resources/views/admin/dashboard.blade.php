@extends('layouts.admin')

{{-- Opcional: Define el título específico de la pestaña y del header --}}
@section('title', 'Admin Dashboard - Principal')
@section('header_title', 'Panel de Administración')
@section('header_subtitle', 'Control total del Sistema de Formatos Digitales')


@section('content')
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