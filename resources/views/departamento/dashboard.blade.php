@extends('layouts.departamento')

@section('title', 'Dashboard Departamento')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fa-solid fa-building text-primary fa-lg"></i>
            <div>
                <h5 class="mb-0">Panel de Departamento</h5>
                <small class="text-muted">Aquí podrás crear solicitudes (tickets) para Sistemas.</small>
            </div>
        </div>

        <hr>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="fw-semibold">Próximo</div>
                        <div class="text-muted small">Crear tickets hacia Admin</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="fw-semibold">Próximo</div>
                        <div class="text-muted small">Ver estatus de mis solicitudes</div>
                    </div>
                </div>
            </div>

            
        </div>

    </div>
</div>
@endsection
