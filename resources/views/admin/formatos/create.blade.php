@extends('layouts.admin')

@section('header_title', 'Nuevo Formato')
@section('header_subtitle', 'Seleccione el tipo de formato a registrar')

@section('content')
<div class="container-fluid">
    <div class="row g-4">

        <div class="col-md-3">
            <a href="{{ route('admin.formatos.a') }}" class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                    <h5>Formato A</h5>
                    <p class="text-muted">Soporte / Desarrollo</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.formatos.b') }}" class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-print fa-3x text-success mb-3"></i>
                    <h5>Formato B</h5>
                    <p class="text-muted">Equipos de impresión</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.formatos.c') }}" class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-desktop fa-3x text-info mb-3"></i>
                    <h5>Formato C</h5>
                    <p class="text-muted">Equipo de cómputo</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.formatos.d') }}" class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-clipboard-list fa-3x text-warning mb-3"></i>
                    <h5>Formato D</h5>
                    <p class="text-muted">Otros servicios</p>
                </div>
            </a>
        </div>

    </div>
</div>
@endsection
