@extends('layouts.admin')

{{-- ======= Configuración de Títulos ======= --}}
@section('title', 'Crear Formato | SEMAHN')
@section('header_title', 'Crear nuevo formato')
@section('header_subtitle', 'Selecciona el tipo de formato a generar')

{{-- ======= Estilos específicos ======= --}}
@section('styles')
<style>
    .card-option {
        border: 1px solid #dee2e6;
        border-radius: var(--border-radius);
        background: var(--card-bg);
        box-shadow: var(--shadow-light);
        padding: 1.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        text-align: center;
        animation: fadeInUp 0.8s ease both;
    }

    .card-option:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }

    .card-option i {
        font-size: 2.2rem;
        color: var(--primary-color);
        margin-bottom: 0.75rem;
    }

    .card-option h5 {
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.3rem;
    }

    .card-option p {
        font-size: 0.9rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
    }

    .card-option .btn {
        border-radius: var(--border-radius);
        padding: 0.4rem 1rem;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(25px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

{{-- ======= Contenido Principal ======= --}}
@section('content')
<div class="container">
    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card-option">
                <i class="fas fa-laptop-code"></i>
                <h5>Formato A</h5>
                <p>Soporte técnico o desarrollo de software.</p>
                <a href="{{ route('admin.formatos.a') }}" class="btn btn-primary btn-sm">Crear</a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card-option">
                <i class="fas fa-desktop"></i>
                <h5>Formato B</h5>
                <p>Equipos de cómputo e impresoras.</p>
                <a href="{{ route('admin.formatos.b') }}" class="btn btn-primary btn-sm">Crear</a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card-option">
                <i class="fas fa-network-wired"></i>
                <h5>Formato C</h5>
                <p>Servicios de redes y telefonía.</p>
                <a href="{{ route('admin.formatos.c') }}" class="btn btn-primary btn-sm">Crear</a>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card-option">
                <i class="fas fa-tools"></i>
                <h5>Formato D</h5>
                <p>Mantenimiento a equipos personales.</p>
                <a href="{{ route('admin.formatos.d') }}" class="btn btn-primary btn-sm">Crear</a>
            </div>
        </div>
    </div>

    <div class="text-end mt-4">
        <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al listado
        </a>
    </div>
</div>
@endsection

