@extends('layouts.admin')

@section('title', 'Tickets')
@section('header_title', 'Tickets')
@section('header_subtitle', 'Bandeja general + detalle')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>Tickets</strong>
            <span class="badge bg-secondary ms-2">{{ $tickets->count() }}</span>
        </div>

        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todos</option>
                @foreach(['pendiente','en_proceso','en_espera','terminado'] as $e)
                    <option value="{{ $e }}" {{ request('estado')===$e?'selected':'' }}>
                        {{ ucfirst(str_replace('_',' ',$e)) }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:70px;">#</th>
                    <th>Departamento</th>
                    <th>Asunto</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Tomado por</th>
                    <th style="width:170px;">Acciones</th>
                </tr>
            </thead>

            <tbody>
            @forelse($tickets as $t)
                @php
                    $badgeEstado = match($t->estado) {
                        'pendiente' => 'bg-secondary',
                        'en_proceso' => 'bg-primary',
                        'en_espera' => 'bg-warning',
                        'terminado' => 'bg-success',
                        default => 'bg-dark'
                    };
                @endphp

                {{-- FILA PRINCIPAL --}}
                <tr>
                    <td><strong>{{ $t->id_ticket }}</strong></td>
                    <td>{{ $t->departamento->nombre }}</td>
                    <td>{{ $t->asunto }}</td>
                    <td><span class="badge bg-info">{{ $t->tipo_atencion }}</span></td>
                    <td><span class="badge {{ $badgeEstado }}">{{ ucfirst(str_replace('_',' ',$t->estado)) }}</span></td>
                    <td>{{ $t->tecnico?->nombre ?? '—' }}</td>

                    <td class="text-end">
                        {{-- BOTÓN PARA DESPLEGAR DETALLE --}}
                        <button type="button"
                                class="btn btn-sm btn-outline-primary btn-toggle"
                                data-target="detalle-{{ $t->id_ticket }}">
                            Ver
                        </button>

                        {{-- (Opcional) BOTÓN DIRECTO A SHOW si quieres --}}
                        {{-- <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.tickets.show',$t->id_ticket) }}">Abrir</a> --}}
                    </td>
                </tr>

                {{-- FILA DETALLE OCULTA --}}
                <tr id="detalle-{{ $t->id_ticket }}" class="d-none bg-light">
                    <td colspan="7">
                        <div class="p-3">

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <div class="border rounded p-3 bg-white">
                                        <h6 class="mb-2">Detalle del Ticket</h6>
                                        <p class="mb-1"><strong>Solicitante:</strong> {{ $t->nombre_solicitante }}</p>
                                        <p class="mb-1"><strong>Contacto:</strong> {{ $t->telefono ?? '—' }}</p>
                                        <p class="mb-1"><strong>Creado:</strong> {{ $t->created_at?->format('d/m/Y H:i') }}</p>
                                        <hr class="my-2">
                                        <p class="mb-0">{{ $t->descripcion }}</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="border rounded p-3 bg-white">
                                        <h6 class="mb-2">Acciones</h6>

                                        {{-- TOMAR (técnico) --}}
                                        @if(!$t->id_tecnico_asignado && $t->estado !== 'terminado')
                                            <form action="{{ route('admin.tickets.take', $t->id_ticket) }}" method="POST" class="mb-2">
                                                @csrf
                                                <button class="btn btn-primary w-100 btn-sm">Tomar ticket</button>
                                            </form>
                                        @endif

                                        {{-- ASIGNAR / REASIGNAR (solo admin) --}}
                                        @if(auth()->user()->isAdmin() && $t->estado !== 'terminado')
                                            <form action="{{ route('admin.tickets.assign', $t->id_ticket) }}" method="POST" class="mb-2">
                                                @csrf
                                                <label class="form-label small mb-1">Asignar técnico</label>
                                                <select name="id_tecnico" class="form-select form-select-sm mb-2" required>
                                                    <option value="">Seleccionar</option>
                                                    @foreach($tecnicos as $tec)
                                                        <option value="{{ $tec->id_usuario }}">
                                                            {{ $tec->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-outline-secondary w-100 btn-sm">Asignar</button>
                                            </form>

                                            <form action="{{ route('admin.tickets.status', $t->id_ticket) }}" method="POST" class="mb-2">
                                                @csrf
                                                <label class="form-label small mb-1">Cambiar estado</label>
                                                <select name="estado" class="form-select form-select-sm mb-2">
                                                    <option value="pendiente" {{ $t->estado==='pendiente'?'selected':'' }}>Pendiente</option>
                                                    <option value="en_proceso" {{ $t->estado==='en_proceso'?'selected':'' }}>En proceso</option>
                                                    <option value="en_espera" {{ $t->estado==='en_espera'?'selected':'' }}>En espera</option>
                                                </select>
                                                <button class="btn btn-outline-warning w-100 btn-sm">Actualizar</button>
                                            </form>

                                        @endif


                                        {{-- FORMATO --}}
@if($t->estado !== 'terminado')
    <form action="{{ route('admin.tickets.set-formato', $t->id_ticket) }}" method="POST" class="mb-2">
        @csrf
        <label class="form-label small mb-1">Formato requerido</label>
        <select name="formato_requerido" class="form-select form-select-sm mb-2" required>
            <option value="">Seleccionar</option>
            @foreach(['A','B','C','D'] as $fx)
                <option value="{{ $fx }}" {{ $t->formato_requerido === $fx ? 'selected' : '' }}>
                    Formato {{ $fx }}
                </option>
            @endforeach
        </select>

        <button class="btn btn-outline-primary w-100 btn-sm">
            Guardar formato
        </button>
    </form>

    <a href="{{ route('admin.tickets.generar-formato', $t->id_ticket) }}"
       class="btn btn-primary w-100 btn-sm mb-2 {{ !$t->formato_generado_en ? '' : 'disabled' }}">
        Generar formato
    </a>

    @if($t->id_servicio)
        <div class="alert alert-success py-2 mb-2">
            Formato generado ✅ (Servicio #{{ $t->id_servicio }})
        </div>
    @endif
@endif


                                        {{-- CERRAR (requiere formato/id_servicio) --}}
                                        @if($t->estado !== 'terminado')
                                            <form action="{{ route('admin.tickets.close', $t->id_ticket) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-success w-100 btn-sm"
                                                        {{ !$t->id_servicio ? 'disabled' : '' }}>
                                                    Cerrar ticket
                                                </button>
                                            </form>
                                            @if(!$t->id_servicio)
                                                <small class="text-muted d-block mt-2">
                                                    Para cerrar, primero genera el formato.
                                                </small>
                                            @endif
                                        @else
                                            <div class="alert alert-success py-2 mb-0">
                                                Ticket terminado ✅
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">Sin tickets</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.target;
            const row = document.getElementById(id);
            if (!row) return;

            row.classList.toggle('d-none');
            btn.textContent = row.classList.contains('d-none') ? 'Ver' : 'Ocultar';
        });
    });
});
</script>
@endsection
