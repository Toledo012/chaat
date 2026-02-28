@extends('layouts.admin')

@section('title', 'Formato A - Soporte / Desarrollo')
@section('header_title', 'Formato A - Soporte / Desarrollo')
@section('header_subtitle', 'Registro y documentación de actividades de soporte')

@section('styles')
    <style>
        .card { border-radius: 10px; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05); border: none; }
        .card-header { background-color: #399e91; color: white; font-weight: 600; }
        .form-control, .form-select { border-radius: 8px; }
        .btn-primary { background-color: #399e91; border-color: #399e91; }
        .btn-primary:hover { background-color: #2f847a; border-color: #2f847a; }
    </style>
@endsection

@section('content')
    <div class="alert alert-info mb-4 d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-2"></i>
        Por favor llena todos los campos obligatorios antes de guardar el formato.
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-laptop-code me-2"></i> Formulario de Registro
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.formatos.a.store') }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Subtipo <span class="text-danger">*</span></label>
                        <select name="subtipo" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="Desarrollo">Desarrollo</option>
                            <option value="Soporte">Soporte</option>
                        </select>
                    </div>

                    {{-- DEPARTAMENTO --}}
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">
                            Departamento <span class="text-danger">*</span>
                        </label>

                        @php
                            $selectedDept = $ticketDeptId ?? old('id_departamento');
                        @endphp

                        {{-- Si viene de ticket: select simple disabled (sin botón) --}}
                        @if($ticketDeptId)
                            <select name="_depto_display"
                                    class="form-select @error('id_departamento') is-invalid @enderror"
                                    disabled>
                                @foreach($departamentos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE) as $dep)
                                    <option value="{{ $dep->id_departamento }}"
                                        {{ $selectedDept == $dep->id_departamento ? 'selected' : '' }}>
                                        {{ $dep->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- Hidden que realmente envía el valor --}}
                            <input type="hidden" name="id_departamento" value="{{ $ticketDeptId }}">

                            {{-- Si NO viene de ticket: input-group con botón "Nuevo" --}}
                        @else
                            <div class="input-group">
                                <select id="selectDepartamentoFormato"
                                        name="id_departamento"
                                        class="form-select shadow-sm border-light-subtle @error('id_departamento') is-invalid @enderror"
                                        required>
                                    <option value="">Selecciona un departamento</option>
                                    @foreach($departamentos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE) as $dep)
                                        <option value="{{ $dep->id_departamento }}"
                                            {{ old('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                            {{ $dep->nombre }}
                                        </option>
                                    @endforeach
                                </select>

                                <button type="button" class="btn btn-outline-success fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#modalCrearDepartamentoFormato">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Si no existe, crea uno aquí mismo.</small>
                        @endif

                        @error('id_departamento')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipo de Atención <span class="text-danger">*</span></label>
                        <select name="tipo_atencion" id="tipoAtencion" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="Memo">Memo</option>
                            <option value="Teléfono">Teléfono</option>
                            <option value="Jefe">Jefe</option>
                            <option value="Usuario">Usuario</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Petición <span class="text-danger">*</span></label>
                    <input type="text" name="peticion" class="form-control" placeholder="Describe brevemente la solicitud" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Servicio *</label>
                        <select name="tipo_servicio" id="tipo_servicio" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="Equipos">Equipos</option>
                            <option value="Redes LAN/WAN">Redes LAN/WAN</option>
                            <option value="Antivirus">Antivirus</option>
                            <option value="Software">Software</option>
                            <option value="otro">Otro…</option>
                        </select>
                        <input type="text" name="tipo_servicio_otro" id="servicioOtro" class="form-control mt-2" style="display:none">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Trabajo Realizado *</label>
                        <select name="trabajo_realizado" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="En sitio">En sitio</option>
                            <option value="Área de producción">Área de producción</option>
                            <option value="Traslado de equipo">Traslado de equipo</option>
                            <option value="Área de Sistemas">Área de sistemas</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Conclusión del Servicio <span class="text-danger">*</span></label>
                    <select name="conclusion_servicio" class="form-select" required>
                        <option value="">Selecciona...</option>
                        <option value="Terminado">Terminado</option>
                        <option value="En proceso">En proceso</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trabajo Específico Realizado <span class="text-danger">*</span></label>
                    <textarea name="detalle_realizado" class="form-control" rows="3" required></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <input id="firmaSolicitante" name="firma_usuario" placeholder="Solicitante" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <input name="firma_tecnico" readonly value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input id="firmaJefe" name="firma_jefe_area" readonly value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2"></textarea>
                </div>

                <input type="hidden" name="id_servicio" value="{{ request('id_servicio') }}">
                <input type="hidden" name="id_ticket" value="{{ request('id_ticket') }}">

                <div class="text-end">
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar</button>
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL CREAR DEPARTAMENTO (solo si NO viene de ticket) --}}
    @if(!$ticketDeptId)
        <div class="modal fade" id="modalCrearDepartamentoFormato" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-building me-2"></i> Registrar Departamento
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="formCrearDepartamentoFormato">
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
                                <input class="form-check-input" type="checkbox" value="1" id="activoDeptoFormato" name="activo" checked>
                                <label class="form-check-label" for="activoDeptoFormato">Activo</label>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-success btn-sm fw-bold px-4 rounded-pill shadow-sm">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ----- Tipo de servicio: mostrar input "Otro" -----
            const tipoServicio = document.getElementById('tipo_servicio');
            const servicioOtro = document.getElementById('servicioOtro');
            tipoServicio.addEventListener('change', () => {
                if (tipoServicio.value === 'otro') {
                    servicioOtro.style.display = 'block';
                    servicioOtro.required = true;
                } else {
                    servicioOtro.style.display = 'none';
                    servicioOtro.required = false;
                    servicioOtro.value = '';
                }
            });

            // ----- Tipo de atención: firma solicitante -----
            const tipoAtencion = document.getElementById('tipoAtencion');
            const firmaSolicitante = document.getElementById('firmaSolicitante');
            const firmaJefe = document.getElementById('firmaJefe');
            tipoAtencion.addEventListener('change', () => {
                if (tipoAtencion.value === 'Jefe') {
                    firmaSolicitante.value = firmaJefe.value;
                    firmaSolicitante.readOnly = true;
                } else {
                    firmaSolicitante.value = '';
                    firmaSolicitante.readOnly = false;
                }
            });

            // ----- Crear departamento desde formato (solo si NO viene de ticket) -----
            const formDepto = document.getElementById('formCrearDepartamentoFormato');
            if (!formDepto) return; // viene de ticket, no hay modal

            formDepto.addEventListener('submit', async (e) => {
                e.preventDefault();

                const btn = formDepto.querySelector('button[type="submit"]');
                btn.disabled = true;

                try {
                    const fd = new FormData(formDepto);

                    const res = await fetch("{{ route('admin.departamentos.quickStore') }}", {
                        method: "POST",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: fd
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        alert(data?.message || 'Error al crear departamento');
                        return;
                    }

                    // Inyectar nueva opción y seleccionarla
                    const sel = document.getElementById('selectDepartamentoFormato');
                    const opt = document.createElement('option');
                    opt.value = data.id_departamento;
                    opt.textContent = data.nombre;
                    opt.selected = true;
                    sel.appendChild(opt);

                    // Cerrar modal y resetear form
                    const modalEl = document.getElementById('modalCrearDepartamentoFormato');
                    bootstrap.Modal.getInstance(modalEl)?.hide();

                    formDepto.reset();
                    document.getElementById('activoDeptoFormato').checked = true;

                } catch (err) {
                    console.error(err);
                    alert('No se pudo crear el departamento.');
                } finally {
                    btn.disabled = false;
                }
            });

        });
    </script>
@endsection
