@extends('layouts.admin')

@section('title', 'Nuevo Formato')
@section('header_title', 'Registro de Servicio')
@section('header_subtitle', 'Seleccione la categoría del formato digital que desea generar')

@section('styles')
<style>
    .selector-card {
        border: none;
        border-radius: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        overflow: hidden;
        position: relative;
    }
    
    .selector-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    .icon-box {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        margin: 0 auto 20px;
        transition: all 0.3s;
    }

    .selector-card:hover .icon-box {
        transform: scale(1.1) rotate(5deg);
    }

    /* Colores temáticos por formato */
    .card-format-a { border-bottom: 5px solid #399e91 !important; }
    .card-format-a .icon-box { background-color: #e0f2f1; color: #399e91; }
    
    .card-format-b { border-bottom: 5px solid #17a2b8 !important; }
    .card-format-b .icon-box { background-color: #e3f2fd; color: #17a2b8; }
    
    .card-format-c { border-bottom: 5px solid #f59e0b !important; }
    .card-format-c .icon-box { background-color: #fff8e1; color: #f59e0b; }
    
    .card-format-d { border-bottom: 5px solid #e91e63 !important; }
    .card-format-d .icon-box { background-color: #fce4ec; color: #e91e63; }

    .format-title {
        font-weight: 800;
        letter-spacing: -0.5px;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .format-desc {
        font-size: 0.85rem;
        color: #718096;
        line-height: 1.4;
    }
    
    .btn-select {
        opacity: 0;
        transition: 0.3s;
        transform: translateY(10px);
    }
    
    .selector-card:hover .btn-select {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    
    <div class="row g-4 justify-content-center">

        {{-- FORMATO A --}}
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.formatos.a') }}" class="card h-100 shadow-sm selector-card card-format-a">
                <div class="card-body p-4 text-center">
                    <div class="icon-box shadow-sm">
                        <i class="fas fa-laptop-code fa-2x"></i>
                    </div>
                    <h4 class="format-title">Formato A</h4>
                    <p class="format-desc">Soporte Técnico General y Desarrollo de Software</p>
                    <div class="btn-select mt-3">
                        <span class="badge bg-primary rounded-pill px-3 py-2">Seleccionar <i class="fas fa-chevron-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>

        {{-- FORMATO B --}}
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.formatos.b') }}" class="card h-100 shadow-sm selector-card card-format-b">
                <div class="card-body p-4 text-center">
                    <div class="icon-box shadow-sm">
                        <i class="fas fa-print fa-2x"></i>
                    </div>
                    <h4 class="format-title">Formato B</h4>
                    <p class="format-desc">Mantenimiento de Equipos e Impresoras Institucionales</p>
                    <div class="btn-select mt-3">
                        <span class="badge bg-info rounded-pill px-3 py-2">Seleccionar <i class="fas fa-chevron-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>

        {{-- FORMATO C --}}
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.formatos.c') }}" class="card h-100 shadow-sm selector-card card-format-c">
                <div class="card-body p-4 text-center">
                    <div class="icon-box shadow-sm">
                        <i class="fas fa-network-wired fa-2x"></i>
                    </div>
                    <h4 class="format-title">Formato C</h4>
                    <p class="format-desc">Infraestructura de Redes, Voz y Datos (Telefonía)</p>
                    <div class="btn-select mt-3">
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Seleccionar <i class="fas fa-chevron-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>

        {{-- FORMATO D --}}
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.formatos.d') }}" class="card h-100 shadow-sm selector-card card-format-d">
                <div class="card-body p-4 text-center">
                    <div class="icon-box shadow-sm">
                        <i class="fas fa-file-signature fa-2x"></i>
                    </div>
                    <h4 class="format-title">Formato D</h4>
                    <p class="format-desc">Entrega, Recepción y Resguardo de Equipo Personal</p>
                    <div class="btn-select mt-3">
                        <span class="badge bg-danger rounded-pill px-3 py-2">Seleccionar <i class="fas fa-chevron-right ms-1"></i></span>
                    </div>
                </div>
            </a>
        </div>

    </div>

    {{-- BOTÓN VOLVER --}}
    <div class="text-center mt-5">
        <a href="{{ route('admin.formatos.index') }}" class="btn btn-link text-muted text-decoration-none small fw-bold">
            <i class="fas fa-arrow-left me-2"></i> Volver al Listado General
        </a>
    </div>
</div>
@endsection