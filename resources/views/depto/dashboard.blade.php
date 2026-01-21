@extends('layouts.admin') {{-- o layout propio de depto --}}

@section('title', 'Dashboard Departamento')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="fa-solid fa-building me-2"></i>Panel del Departamento</h4>
            <small class="text-muted">Aquí puedes crear y consultar tus tickets</small>
        </div>

        <a href="{{ route('tickets.index') }}" class="btn btn-primary">
            <i class="fa-solid fa-ticket me-1"></i> Ir a Tickets
        </a>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="mb-2"><i class="fa-solid fa-plus me-2"></i>Crear una solicitud</h6>
                    <p class="text-muted mb-3">Genera un ticket para que Soporte/Sistemas atienda tu problema.</p>
                    <a href="{{ route('tickets.index') }}#modalCrearTicket" class="btn btn-outline-primary"
                       data-bs-toggle="modal" data-bs-target="#modalCrearTicket">
                        <i class="fa-solid fa-plus me-1"></i> Crear Ticket
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="mb-2"><i class="fa-solid fa-list-check me-2"></i>Consultar tickets</h6>
                    <p class="text-muted mb-3">Revisa el estado de tus tickets: pendiente, en proceso o terminado.</p>
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-eye me-1"></i> Ver Mis Tickets
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
