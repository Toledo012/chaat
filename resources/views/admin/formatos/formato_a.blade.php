@extends('layouts.admin')

@section('title', 'Formato A - Soporte y Desarrollo')
@section('header_title', 'Formato A - Soporte y Desarrollo')
@section('header_subtitle', 'Registro de servicios de soporte técnico o desarrollo institucional')

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
        <div class="card-header"><i class="fas fa-headset me-2"></i>Formulario de Formato A</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.formatos.a.store') }}">
                @csrf

                {{-- Hiddens del servicio/ticket --}}
                <input type="hidden" name="id_servicio" value="{{ request('id_servicio') }}">
                <input type="hidden" name="id_ticket"   value="{{ request('id_ticket') }}">

                {{-- ── DEPARTAMENTO ── --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">Departamento <span class="text-danger">*</span></label>

                        @if($ticketDeptId)
                            {{-- Viene de ticket: solo mostrar --}}
                            <select name="id_departamento" class="form-select" disabled>
                                @foreach($departamentos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE) as $dep)
                                    <option value="{{ $dep->id_departamento }}"
                                        {{ $ticketDeptId == $dep->id_departamento ? 'selected' : '' }}>
                                        {{ $dep->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="id_departamento" value="{{ $ticketDeptId }}">
                        @else
                            <div class="input-group">
                                <select id="selectDepartamentoFormatoA" name="id_departamento" class="form-select" required>
                                    <option value="">Selecciona un departamento</option>
                                    @foreach($departamentos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE) as $dep)
                                        <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-success fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#modalCrearDepartamentoFormatoA">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Si no existe, crea uno aquí mismo.</small>
                        @endif
                    </div>
                </div>

                <hr>

                {{-- ── SUBTIPO ── --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Subtipo <span class="text-danger">*</span></label>
                        <select name="subtipo" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="Desarrollo">Desarrollo</option>
                            <option value="Soporte">Soporte</option>
                        </select>
                    </div>

                    {{-- ── TIPO DE ATENCIÓN + BLOQUE MEMO ── --}}
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

                {{-- ── PETICIÓN ── --}}
                <div class="mb-3">
                    <label class="form-label">Petición <span class="text-danger">*</span></label>
                    <input type="text" name="peticion" class="form-control"
                           placeholder="Describe brevemente la solicitud" required>
                </div>

                {{-- ── TIPO DE SERVICIO + TRABAJO REALIZADO ── --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Servicio <span class="text-danger">*</span></label>
                        <select name="tipo_servicio" id="tipo_servicio" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="Equipos">Equipos</option>
                            <option value="Redes LAN/WAN">Redes LAN/WAN</option>
                            <option value="Antivirus">Antivirus</option>
                            <option value="Software">Software</option>
                            <option value="otro">Otro…</option>
                        </select>
                        <input type="text" name="tipo_servicio_otro" id="servicioOtro"
                               class="form-control mt-2" style="display:none"
                               placeholder="Especifica el tipo de servicio">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Trabajo Realizado <span class="text-danger">*</span></label>
                        <select name="trabajo_realizado" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="En sitio">En sitio</option>
                            <option value="Área de producción">Área de producción</option>
                            <option value="Traslado de equipo">Traslado de equipo</option>
                            <option value="Área de Sistemas">Área de sistemas</option>
                        </select>
                    </div>
                </div>

                {{-- ── CONCLUSIÓN ── --}}
                <div class="mb-3">
                    <label class="form-label">Conclusión del Servicio <span class="text-danger">*</span></label>
                    <select name="conclusion_servicio" class="form-select" required>
                        <option value="">Selecciona...</option>
                        <option value="Terminado">Terminado</option>
                        <option value="En proceso">En proceso</option>
                    </select>
                </div>

                {{-- ── DETALLE REALIZADO ── --}}
                <div class="mb-3">
                    <label class="form-label">Trabajo Específico Realizado <span class="text-danger">*</span></label>
                    <textarea name="detalle_realizado" class="form-control" rows="3" required></textarea>
                </div>

                {{-- ── OBSERVACIONES ── --}}
                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2"
                              placeholder="Opcional..."></textarea>
                </div>

                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-signature me-1"></i> Firmas y Validación</h6>

                {{-- ── FIRMAS ── --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Solicitante <span class="text-danger">*</span></label>
                        <input id="firmaSolicitante" name="firma_usuario"
                               placeholder="Nombre de quien solicita"
                               class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Técnico responsable</label>
                        <input name="firma_tecnico" readonly
                               class="form-control bg-light"
                               value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jefe de Área</label>
                        <input id="firmaJefe" name="firma_jefe_area" readonly
                               class="form-control bg-light"
                               value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}">
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar</button>
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL CREAR DEPARTAMENTO (solo si NO viene de ticket) ── --}}
    @if(!$ticketDeptId)
        <div class="modal fade" id="modalCrearDepartamentoFormatoA" tabindex="-1" aria-hidden="true">
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
                                <input type="text" name="nombre" class="form-control shadow-sm"
                                       required maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Descripción</label>
                                <input type="text" name="descripcion" class="form-control shadow-sm" maxlength="100">
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="submit" class="btn btn-success fw-bold px-4 rounded-pill">
                                <i class="fas fa-save me-1"></i> Guardar
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
        document.addEventListener('DOMContentLoaded', () => {

            // ── 1. Tipo de atención → mostrar/ocultar bloque Memo ──────────────────
            const tipoAtencion = document.getElementById('tipoAtencion');
            const bloqueMemo   = document.getElementById('bloqueMemo');
            const inputMemo    = document.getElementById('num_memo');

            tipoAtencion.addEventListener('change', () => {
                if (tipoAtencion.value === 'Memo') {
                    bloqueMemo.style.display = 'block';
                    inputMemo.setAttribute('required', 'required');
                } else {
                    bloqueMemo.style.display = 'none';
                    inputMemo.removeAttribute('required');
                    inputMemo.value = '';
                }


        });

            // ── 2. Tipo de servicio → mostrar input "Otro" ──────────────────────────
            const tipoServicio = document.getElementById('tipo_servicio');
            const servicioOtro = document.getElementById('servicioOtro');

            tipoServicio.addEventListener('change', () => {
                if (tipoServicio.value === 'otro') {
                    servicioOtro.style.display = 'block';
                    servicioOtro.required      = true;
                } else {
                    servicioOtro.style.display = 'none';
                    servicioOtro.required      = false;
                    servicioOtro.value         = '';
                }
            });

            // ── 3. Crear departamento desde el modal (solo si existe el form) ───────
            const formDepto = document.getElementById('formCrearDepartamentoFormato');
            if (!formDepto) return;

            formDepto.addEventListener('submit', async (e) => {
                e.preventDefault();

                const btn = formDepto.querySelector('button[type="submit"]');
                btn.disabled    = true;
                btn.textContent = 'Guardando...';

                try {
                    const resp = await fetch('{{ route("admin.departamentos.store") }}', {
                        method:  'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept':       'application/json',
                        },
                        body: JSON.stringify(Object.fromEntries(new FormData(formDepto))),
                    });

                    const data = await resp.json();

                    if (data.success) {
                        const select = document.getElementById('selectDepartamentoFormatoA');
                        const opt    = new Option(data.departamento.nombre, data.departamento.id_departamento, true, true);
                        select.appendChild(opt);
                        bootstrap.Modal.getInstance(
                            document.getElementById('modalCrearDepartamentoFormatoA')
                        ).hide();
                        formDepto.reset();
                    } else {
                        alert('Error al crear el departamento.');
                    }
                } catch {
                    alert('Error de conexión.');
                } finally {
                    btn.disabled    = false;
                    btn.textContent = 'Guardar';
                }
            });
        });
    </script>
@endsection
