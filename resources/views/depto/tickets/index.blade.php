@extends('layouts.admin') {{-- o tu layout de depto, si tienes uno --}}

@section('title', 'Mis Tickets')
@section('content')
<div class="container py-4">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Hay errores en el formulario:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0"><i class="fa-solid fa-ticket me-2"></i>Mis Tickets</h4>
            <small class="text-muted">Crea y consulta tus solicitudes</small>
        </div>

        {{-- Botón Crear (modal) --}}
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearTicket">
            <i class="fa-solid fa-plus me-1"></i> Crear Ticket
        </button>
    </div>

    {{-- Tabla --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:90px;">ID</th>
                            <th>Asunto</th>
                            <th style="width:160px;">Tipo</th>
                            <th style="width:140px;">Estado</th>
                            <th style="width:160px;">Fecha</th>
                            <th style="width:120px;">Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($tickets as $t)
                        {{-- fila principal --}}
                        <tr>
                            <td><strong>#{{ $t->id_ticket }}</strong></td>
                            <td>
                                <div class="fw-semibold">{{ $t->asunto }}</div>
                                <small class="text-muted">{{ $t->nombre_solicitante }} · {{ $t->telefono ?? 'Sin teléfono' }}</small>
                            </td>

                            <td>
                                @php
                                    $tipo = $t->tipo_atencion;
                                    $tipoLabel = match($tipo) {
                                        'equipo' => 'Equipo',
                                        'red_wifi' => 'Red/WiFi',
                                        'software_programas' => 'Software/Programas',
                                        'otro' => 'Otro',
                                        default => $tipo
                                    };
                                @endphp
                                <span class="badge bg-info">{{ $tipoLabel }}</span>
                            </td>

                            <td>
                                @php
                                    $estado = $t->estado ?? 'pendiente';
                                    $badge = match($estado) {
                                        'pendiente' => 'bg-secondary',
                                        'en_proceso' => 'bg-primary',
                                        'en_espera' => 'bg-warning text-dark',
                                        'terminado' => 'bg-success',
                                        default => 'bg-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ strtoupper($estado) }}</span>
                            </td>

                            <td>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') }}
                                </small>
                            </td>

                            <td>
                                <button class="btn btn-outline-secondary btn-sm"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#det-{{ $t->id_ticket }}"
                                        aria-expanded="false">
                                    <i class="fa-solid fa-eye me-1"></i> Ver
                                </button>
                            </td>
                        </tr>

                        {{-- fila detalle (colapsable) --}}
                        <tr class="collapse" id="det-{{ $t->id_ticket }}">
                            <td colspan="6" class="bg-light">
                                <div class="p-3">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="small text-muted">Solicitante</div>
                                            <div class="fw-semibold">{{ $t->nombre_solicitante }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="small text-muted">Correo</div>
                                            <div class="fw-semibold">{{ $t->correo_solicitante ?? 'No proporcionado' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="small text-muted">Teléfono</div>
                                            <div class="fw-semibold">{{ $t->telefono ?? 'No proporcionado' }}</div>
                                        </div>

                                        <div class="col-12">
                                            <div class="small text-muted">Descripción</div>
                                            <div class="border rounded p-2 bg-white">
                                                {{ $t->descripcion }}
                                            </div>
                                        </div>

                                        {{-- Opcional si quieres mostrar técnico asignado/servicio --}}
                                        {{-- 
                                        <div class="col-md-6">
                                            <div class="small text-muted">Técnico asignado</div>
                                            <div class="fw-semibold">{{ optional($t->tecnico)->nombre ?? 'Sin asignar' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="small text-muted">Formato / Servicio</div>
                                            <div class="fw-semibold">{{ $t->id_servicio ? 'Generado (Servicio #'.$t->id_servicio.')' : 'Aún no generado' }}</div>
                                        </div>
                                        --}}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No hay tickets aún. Crea el primero con el botón <strong>Crear Ticket</strong>.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>

{{-- Modal Crear Ticket --}}
<div class="modal fade" id="modalCrearTicket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('tickets.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-plus me-2"></i>Crear Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nombre solicitante *</label>
                            <input type="text" name="nombre_solicitante" class="form-control" required value="{{ old('nombre_solicitante') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Correo</label>
                            <input type="email" name="correo_solicitante" class="form-control" value="{{ old('correo_solicitante') }}">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Asunto *</label>
                            <input type="text" name="asunto" class="form-control" required value="{{ old('asunto') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipo de atención *</label>
                            <select name="tipo_atencion" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <option value="equipo" {{ old('tipo_atencion')=='equipo'?'selected':'' }}>Equipo</option>
                                <option value="red_wifi" {{ old('tipo_atencion')=='red_wifi'?'selected':'' }}>Red/WiFi</option>
                                <option value="software_programas" {{ old('tipo_atencion')=='software_programas'?'selected':'' }}>Software/Programas</option>
                                <option value="otro" {{ old('tipo_atencion')=='otro'?'selected':'' }}>Otro</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción *</label>
                            <textarea name="descripcion" class="form-control" rows="4" required>{{ old('descripcion') }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane me-1"></i> Enviar Ticket
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
