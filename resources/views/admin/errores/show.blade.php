@extends('layouts.admin')

@section('title', 'Detalle de error')
@section('header_title', 'Errores del Sistema')
@section('header_subtitle', 'Detalle del fallo #' . $error->id_error)

@section('content')
<div class="container-fluid">
    <a href="{{ route('admin.errores.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>

    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0">{{ class_basename($error->clase) }}</h5>
                <small class="text-body-secondary">{{ $error->clase }}</small>
            </div>

            <form method="POST" action="{{ route('admin.errores.atender', $error->id_error) }}">
                @csrf
                @method('PUT')
                @if($error->atendido_en)
                    <button class="btn btn-sm btn-outline-warning"><i class="fas fa-rotate-left me-1"></i> Reabrir</button>
                @else
                    <button class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i> Marcar como atendido</button>
                @endif
            </form>
        </div>

        <div class="card-body">
            <p class="fw-semibold mb-4" style="word-break: break-word;">{{ $error->mensaje }}</p>

            <dl class="row mb-0">
                <dt class="col-sm-3">Ubicación</dt>
                <dd class="col-sm-9"><code>{{ $error->archivo }}:{{ $error->linea }}</code></dd>

                <dt class="col-sm-3">Petición</dt>
                <dd class="col-sm-9">{{ $error->metodo }} {{ $error->url ?? '(consola)' }}</dd>

                <dt class="col-sm-3">Usuario</dt>
                <dd class="col-sm-9">{{ $error->username ?? 'Sin sesión' }}</dd>

                <dt class="col-sm-3">IP / Navegador</dt>
                <dd class="col-sm-9"><small>{{ $error->ip }} — {{ $error->navegador }}</small></dd>

                <dt class="col-sm-3">Ocurrencias</dt>
                <dd class="col-sm-9">{{ $error->ocurrencias }} (primera: {{ $error->primera_vez }}, última: {{ $error->ultima_vez }})</dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    @if($error->atendido_en)
                        Atendido el {{ $error->atendido_en }} por {{ $error->atendido_por_username ?? '—' }}
                    @else
                        Pendiente
                    @endif
                </dd>
            </dl>

            <h6 class="mt-4">Traza</h6>
            <pre class="bg-body-tertiary border rounded p-3 small" style="max-height: 420px; overflow: auto; white-space: pre-wrap;">{{ $error->traza }}</pre>
        </div>
    </div>
</div>
@endsection
