@extends('layouts.admin')

@section('title', 'Tickets')
@section('header_title', 'Gestión de Tickets')
@section('header_subtitle', 'Solicitudes de departamentos y usuarios, asignación y seguimiento')

@section('content')
<div class="container-fluid">

    {{-- CARD CONTENEDORA --}}
    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-ticket-alt text-primary fa-lg"></i>
                <div>
                    <h5 class="mb-0">Tickets</h5>
                    <small class="text-muted">Bandeja principal del Admin</small>
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearTicket">
    <i class="fas fa-plus me-1"></i> Nuevo Ticket
</button>

            {{-- FILTROS --}}
            <form method="GET" class="d-flex gap-2 flex-wrap">
                <input type="text"
                       name="buscar"
                       class="form-control form-control-sm"
                       placeholder="Buscar folio o título..."
                       value="{{ $qBuscar ?? '' }}">

                <select name="estado" class="form-select form-select-sm">
                    <option value="">Estado (todos)</option>
                    @foreach(['nuevo','asignado','en_proceso','en_espera','completado','cancelado'] as $st)
                        <option value="{{ $st }}" @selected(($qEstado ?? '') === $st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                    @endforeach
                </select>

                <select name="tipo_formato" class="form-select form-select-sm">
                    <option value="">Formato (todos)</option>
                    @foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $k=>$v)
                        <option value="{{ $k }}" @selected(($qTipo ?? '') === $k)>{{ $v }}</option>
                    @endforeach
                </select>

                <select name="prioridad" class="form-select form-select-sm">
                    <option value="">Prioridad (todas)</option>
                    @foreach(['baja'=>'Baja','media'=>'Media','alta'=>'Alta'] as $k=>$v)
                        <option value="{{ $k }}" @selected(($qPrioridad ?? '') === $k)>{{ $v }}</option>
                    @endforeach
                </select>

                <button class="btn btn-sm btn-primary">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>

                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                    Limpiar
                </a>
            </form>
        </div>

        {{-- BODY --}}
        <div class="card-body">

            @if($tickets->count() === 0)
                <div class="alert alert-info mb-0">
                    No hay tickets con esos filtros.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Folio</th>
                                <th>Título</th>
                                <th>Formato</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                
                                <th>Asignado a</th>
                                <th>Detalles</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $t)
                                <tr>
                                    <td class="fw-semibold">{{ $t->folio }}</td>
                                    <td style="min-width: 250px;">
                                        <div class="fw-semibold">{{ $t->titulo }}</div>
                                        @if($t->descripcion)
                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($t->descripcion, 90) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge text-bg-secondary">
                                            {{ strtoupper($t->tipo_formato) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $prioClass = match($t->prioridad) {
                                                'alta' => 'text-bg-danger',
                                                'media' => 'text-bg-warning',
                                                default => 'text-bg-success',
                                            };
                                        @endphp
                                        <span class="badge {{ $prioClass }}">{{ ucfirst($t->prioridad) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $stClass = match($t->estado) {
                                                'nuevo' => 'text-bg-primary',
                                                'asignado' => 'text-bg-info',
                                                'en_proceso' => 'text-bg-warning',
                                                'en_espera' => 'text-bg-secondary',
                                                'completado' => 'text-bg-success',
                                                'cancelado' => 'text-bg-danger',
                                                default => 'text-bg-dark',
                                            };
                                        @endphp
                                        <span class="badge {{ $stClass }}">{{ ucfirst(str_replace('_',' ',$t->estado)) }}</span>
                                    </td>

<td style="min-width: 220px;">
  @if($t->asignadoA)
      <span class="fw-semibold">{{ $t->asignadoA->username }}</span>
  @else
      <span class="text-muted">Sin asignar</span>
  @endif
</td>

<td style="min-width: 250px;">
    <div class="fw-semibold">{{ $t->titulo }}</div>

    @if($t->solicitante)
        <small class="text-muted">
            <i class="fas fa-user me-1"></i>
            Solicitante: {{ $t->solicitante }}
        </small>
    @endif

    @if($t->descripcion)
        <br>
        <small class="text-muted">
            {{ \Illuminate\Support\Str::limit($t->descripcion, 90) }}
        </small>
    @endif
</td>






                                    <td class="text-end" style="min-width: 320px;">
                                        {{-- Asignar --}}
                                        <form method="POST" action="{{ route('admin.tickets.asignar', $t->id_ticket) }}" class="d-inline-flex gap-2">
                                            @csrf
                                            <select name="asignado_a" class="form-select form-select-sm" @disabled(in_array($t->estado,['cancelado','completado']))>
                                                <option value="">Asignar a técnico...</option>
                                                @foreach($tecnicos as $tec)
                                                    <option value="{{ $tec->id_cuenta }}">
                                                        {{ $tec->username }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button class="btn btn-sm btn-success"
                                                    @disabled(in_array($t->estado,['cancelado','completado']))>
                                                <i class="fas fa-user-check me-1"></i> Asignar
                                            </button>
                                        </form>
<a class="btn btn-sm btn-primary"
   href="{{ route('admin.tickets.completar', $t->id_ticket) }}"
   @if(in_array($t->estado, ['cancelado','completado'])) aria-disabled="true" style="pointer-events:none;opacity:.6;" @endif>
    <i class="fas fa-clipboard-check me-1"></i> Completar
</a>

                                        {{-- Cancelar --}}
                                        <form method="POST" action="{{ route('admin.tickets.cancelar', $t->id_ticket) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('¿Cancelar este ticket?')"
                                                    @disabled($t->estado === 'completado' || $t->estado === 'cancelado')>
                                                <i class="fas fa-ban me-1"></i> Cancelar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $tickets->links() }}
                </div>
            @endif

        </div>
    </div>
</div>

<!-- Modal: Crear Ticket -->
<div class="modal fade" id="modalCrearTicket" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-plus-circle text-success me-2"></i> Crear nuevo Ticket
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="POST" action="{{ route('admin.tickets.store') }}">
        @csrf
        <div class="modal-body">

          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Título</label>
              <input type="text" name="titulo" class="form-control" required maxlength="255"
                     value="{{ old('titulo') }}">
            </div>

            <div class="col-md-4">
  <label class="form-label">Solicitante</label>
  <input type="text" name="solicitante" class="form-control" required maxlength="150" value="{{ old('solicitante') }}">
</div>

            <div class="col-md-2">
              <label class="form-label">Prioridad</label>
              <select name="prioridad" class="form-select" required>
                <option value="baja" @selected(old('prioridad')==='baja')>Baja</option>
                <option value="media" @selected(old('prioridad')==='media')>Media</option>
                <option value="alta" @selected(old('prioridad')==='alta')>Alta</option>
              </select>
            </div>

            <div class="col-md-2">
              <label class="form-label">Formato</label>
              <select name="tipo_formato" class="form-select" required>
                <option value="a" @selected(old('tipo_formato')==='a')>A</option>
                <option value="b" @selected(old('tipo_formato')==='b')>B</option>
                <option value="c" @selected(old('tipo_formato')==='c')>C</option>
                <option value="d" @selected(old('tipo_formato')==='d')>D</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="4"
                        placeholder="Describe la solicitud...">{{ old('descripcion') }}</textarea>
            </div>
          </div>

          @if($errors->any())
            <div class="alert alert-danger mt-3 mb-0">
              <strong>Ups:</strong> Corrige lo siguiente:
              <ul class="mb-0">
                @foreach($errors->all() as $e)
                  <li>{{ $e }}</li>
                @endforeach
              </ul>
            </div>
          @endif

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i> Guardar Ticket
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
@if($errors->any())
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = new bootstrap.Modal(document.getElementById('modalCrearTicket'));
    modal.show();
  });
</script>
@endif



@endsection
