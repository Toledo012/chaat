@extends('layouts.admin')

@section('title', 'Formato D - Mantenimiento Personal')
@section('header_title', 'Formato D - Mantenimiento Personal')
@section('header_subtitle', 'Entrega y recepción de equipo institucional')

@section('styles')
    <style>
        .card-header { background-color: #399e91; color: white; font-weight: 600; }
        .form-control, .form-select { border-radius: 8px; }
        .btn-primary { background-color: #399e91; border-color: #399e91; }
        .btn-primary:hover { background-color: #2f847a; border-color: #2f847a; }
        .alert-info { background-color: #d1f0eb; border-color: #399e91; color: #25685d; font-weight: 500; }


        /* Animación suave del bloque memo */
        #bloqueMemo {
            overflow: hidden;
            transition: all 0.25s ease;
        }

    </style>
@endsection

@section('content')
    <div class="alert alert-info mb-4 d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-2"></i>
        Llena todos los campos obligatorios antes de guardar el formato.
    </div>

    <div class="card shadow border-0">
        <div class="card-header"><i class="fas fa-tools me-2"></i>Formulario de Formato D</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.formatos.d.store') }}">
                @csrf

                {{-- Hiddens del servicio/ticket --}}
                <input type="hidden" name="id_servicio" value="{{ request('id_servicio') }}">
                <input type="hidden" name="id_ticket" value="{{ request('id_ticket') }}">

                {{-- DEPARTAMENTO: solo para mostrar/crear, no se guarda en formato_d sino en servicios --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Departamento</label>

                        @if($ticketDeptId)
                            {{-- Viene de ticket: solo mostrar, el valor ya está en servicios --}}
                            <select class="form-select" disabled>
                                @foreach($departamentos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE) as $dep)
                                    <option value="{{ $dep->id_departamento }}"
                                        {{ $ticketDeptId == $dep->id_departamento ? 'selected' : '' }}>
                                        {{ $dep->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            {{-- No viene de ticket: permite crear depto nuevo si no existe --}}
                            <div class="input-group">
                                <select id="selectDepartamentoFormatoD" class="form-select shadow-sm border-light-subtle">
                                    <option value="">Selecciona un departamento</option>
                                    @foreach($departamentos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE) as $dep)
                                        <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-success fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#modalCrearDepartamentoFormatoD">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Si no existe, crea uno aquí mismo.</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Atención <span class="text-danger">*</span></label>
                        <select name="tipo_atencion" id="tipoAtencion" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="Memo">Memo</option>
                            <option value="Teléfono">Teléfono</option>
                            <option value="Jefe">Jefe</option>
                            <option value="Usuario">Usuario</option>
                        </select>

                        {{-- Campo condicional: solo visible cuando se elige "Memo" --}}
                        <div id="bloqueMemo" style="display:none;" class="mt-2">
                            <input type="text"
                                   name="num_memo"
                                   id="num_memo"
                                   class="form-control"
                                   placeholder="Número o folio del memo"
                                   maxlength="100">
                            <small class="text-muted">Ingresa el número o folio del memo de referencia.</small>
                        </div>
                    </div>


                </div>

                <hr>

                {{-- EQUIPO, MARCA, MODELO --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Equipo <span class="text-danger">*</span></label>
                        <input name="equipo" class="form-control" value="{{ old('equipo') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Marca <span class="text-danger">*</span></label>
                        <input name="marca" class="form-control" value="{{ old('marca') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Modelo <span class="text-danger">*</span></label>
                        <input name="modelo" class="form-control" value="{{ old('modelo') }}" required>
                    </div>
                </div>

                {{-- SERIE --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Número de Serie</label>
                        <input name="serie" class="form-control" value="{{ old('serie') }}">
                    </div>
                </div>

                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-signature me-1"></i> Firmas y Validaciones</h6>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label>Firma solicitante <span class="text-danger">*</span></label>
                        <input id="firmaSolicitante" name="firma_usuario" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Responsable</label>
                        <input name="firma_tecnico" readonly class="form-control bg-light" value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}">
                    </div>
                    <div class="col-md-4">
                        <label>Jefe de Área</label>
                        <input id="firmaJefe" name="firma_jefe_area" readonly class="form-control bg-light"
                               value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}">                    </div>
                </div>


                {{-- OBSERVACIONES --}}
                <div class="mb-4">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones') }}</textarea>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar</button>
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL CREAR DEPARTAMENTO (solo si NO viene de ticket) --}}
    @if(!$ticketDeptId)
        <div class="modal fade" id="modalCrearDepartamentoFormatoD" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-building me-2"></i> Registrar Departamento
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formCrearDepartamentoFormatoD">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nombre *</label>
                                <input type="text" name="nombre" class="form-control shadow-sm" required maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Descripción</label>
                                <textarea name="descripcion" class="form-control shadow-sm" rows="3"></textarea>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="activoDeptoFormatoD" name="activo" checked>
                                <label class="form-check-label" for="activoDeptoFormatoD">Activo</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success btn-sm fw-bold px-4 rounded-pill shadow-sm">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const formDepto = document.getElementById('formCrearDepartamentoFormatoD');
            if (!formDepto) return;

            formDepto.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = formDepto.querySelector('button[type="submit"]');
                btn.disabled = true;
                try {
                    const res = await fetch("{{ route('admin.departamentos.quickStore') }}", {
                        method: "POST",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: new FormData(formDepto)
                    });
                    const data = await res.json();
                    if (!res.ok) { alert(data?.message || 'Error al crear departamento'); return; }

                    const sel = document.getElementById('selectDepartamentoFormatoD');
                    const opt = document.createElement('option');
                    opt.value = data.id_departamento;
                    opt.textContent = data.nombre;
                    opt.selected = true;
                    sel.appendChild(opt);

                    bootstrap.Modal.getInstance(document.getElementById('modalCrearDepartamentoFormatoD'))?.hide();
                    formDepto.reset();
                    document.getElementById('activoDeptoFormatoD').checked = true;
                } catch (err) {
                    console.error(err);
                    alert('No se pudo crear el departamento.');
                } finally {
                    btn.disabled = false;
                }
            });



            // ── 1. Tipo de atención → mostrar/ocultar bloque Memo ──────────────────
            const tipoAtencion = document.getElementById('tipoAtencion');
            const bloqueMemo   = document.getElementById('bloqueMemo');
            const inputMemo    = document.getElementById('num_memo');

            //TIPO DE ATENCION -> MEMO
            tipoAtencion.addEventListener('change', () => {
                if (tipoAtencion.value === 'Memo') {
                    bloqueMemo.style.display = 'block';
                    inputMemo.setAttribute('required', 'required');
                } else {
                    bloqueMemo.style.display = 'none';
                    inputMemo.removeAttribute('required');
                    inputMemo.value = '';
                }


                // Cuando seleccionan "Jefe", autocompleta firma solicitante
                const firmaSolicitante = document.getElementById('firmaSolicitante');
                const firmaJefe        = document.getElementById('firmaJefe');
                if (tipoAtencion.value === 'Jefe') {
                    firmaSolicitante.value    = firmaJefe.value;
                    firmaSolicitante.readOnly = true;
                } else {
                    if (firmaSolicitante.readOnly) firmaSolicitante.value = '';
                    firmaSolicitante.readOnly = false;
                }
            });
        });
    </script>
@endsection
