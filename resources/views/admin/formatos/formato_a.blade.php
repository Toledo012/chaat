@extends('layouts.admin')

@section('title', 'Formato A - Soporte / Desarrollo')
@section('header_title', 'Formato A - Registro de Actividades')
@section('header_subtitle', 'Documentación técnica de soporte y desarrollo de software')

@section('styles')
<style>
    .card-form { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .form-section-title { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #399e91; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px; margin-bottom: 20px; letter-spacing: 0.5px; }
    .form-label { font-size: 0.8rem; font-weight: 700; color: #495057; text-transform: uppercase; margin-bottom: 5px; }
    .form-control, .form-select { border-radius: 10px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.9rem; transition: all 0.2s; }
    .form-control:focus, .form-select:focus { border-color: #399e91; box-shadow: 0 0 0 0.25rem rgba(57, 158, 145, 0.1); }
    .input-group-text { background-color: #f8f9fa; border-radius: 10px; }
    .badge-info-custom { background-color: #e0f2f1; color: #00796b; border-radius: 8px; padding: 10px 15px; font-size: 0.85rem; font-weight: 600; }
</style>
@endsection

@section('content')
<div class="container-fluid px-2">

    {{-- AVISO INFORMATIVO --}}
    <div class="badge-info-custom mb-4 d-flex align-items-center shadow-sm">
        <i class="fas fa-info-circle me-3 fa-lg"></i>
        <span>Por favor, asegúrate de completar todos los campos marcados con asterisco (*) antes de procesar el guardado.</span>
    </div>

    <div class="card card-form shadow-sm">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('admin.formatos.a.store') }}">
                @csrf

                {{-- SECCIÓN 1: CLASIFICACIÓN Y ORIGEN --}}
                <div class="form-section-title"><i class="fas fa-layer-group me-2"></i>Clasificación y Origen</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Subtipo de Actividad <span class="text-danger">*</span></label>
                        <select name="subtipo" class="form-select shadow-sm" required>
                            <option value="">Selecciona...</option>
                            <option value="Desarrollo">Desarrollo de Software</option>
                            <option value="Soporte">Soporte Técnico</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Departamento Solicitante <span class="text-danger">*</span></label>
                        <select name="id_departamento" class="form-select shadow-sm @error('id_departamento') is-invalid @enderror" required>
                            <option value="">Selecciona un departamento</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Medio de Atención <span class="text-danger">*</span></label>
                        <select name="tipo_atencion" id="tipoAtencion" class="form-select shadow-sm" required>
                            <option value="">Selecciona...</option>
                            <option value="Memo">Memorándum</option>
                            <option value="Teléfono">Vía Telefónica</option>
                            <option value="Jefe">Instrucción de Jefe</option>
                            <option value="Usuario">Solicitud de Usuario</option>
                        </select>
                    </div>
                </div>

                {{-- SECCIÓN 2: DETALLES DE LA SOLICITUD --}}
                <div class="form-section-title"><i class="fas fa-edit me-2"></i>Detalles de la Petición</div>
                <div class="mb-4">
                    <label class="form-label">Asunto de la Petición <span class="text-danger">*</span></label>
                    <input type="text" name="peticion" class="form-control shadow-sm" placeholder="Resumen breve de la solicitud recibida" required>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Servicio <span class="text-danger">*</span></label>
                        <select name="tipo_servicio" id="tipo_servicio" class="form-select shadow-sm" required>
                            <option value="">Selecciona...</option>
                            <option value="Equipos">Equipos de Cómputo</option>
                            <option value="Redes LAN/WAN">Redes LAN / WAN</option>
                            <option value="Antivirus">Protección Antivirus</option>
                            <option value="Software">Instalación de Software</option>
                            <option value="otro">Otro (Especificar)</option>
                        </select>
                        <input type="text" name="tipo_servicio_otro" id="servicioOtro" class="form-control mt-2 shadow-sm border-warning" placeholder="¿Cuál otro servicio?" style="display:none">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Modalidad del Trabajo <span class="text-danger">*</span></label>
                        <select name="trabajo_realizado" class="form-select shadow-sm" required>
                            <option value="">Selecciona...</option>
                            <option value="En sitio">Atención en Sitio</option>
                            <option value="Área de producción">Área de Producción</option>
                            <option value="Traslado de equipo">Traslado de Equipo</option>
                        </select>
                    </div>
                </div>

                {{-- SECCIÓN 3: CONCLUSIÓN Y DESARROLLO --}}
                <div class="form-section-title"><i class="fas fa-check-circle me-2"></i>Conclusión y Bitácora</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Estatus del Servicio <span class="text-danger">*</span></label>
                        <select name="conclusion_servicio" class="form-select shadow-sm w-auto" required>
                            <option value="">Selecciona...</option>
                            <option value="Terminado">Servicio Terminado</option>
                            <option value="En proceso">En Proceso / Pendiente</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descripción Detallada del Trabajo <span class="text-danger">*</span></label>
                        <textarea name="detalle_realizado" class="form-control shadow-sm" rows="4" placeholder="Describe las acciones técnicas realizadas de manera específica" required></textarea>
                    </div>
                </div>

                {{-- SECCIÓN 4: VALIDACIÓN Y FIRMAS --}}
                <div class="form-section-title"><i class="fas fa-signature me-2"></i>Validación y Firmas</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Firma Solicitante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input id="firmaSolicitante" name="firma_usuario" placeholder="Nombre de quien recibe" class="form-control shadow-sm" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Firma Técnico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-gear"></i></span>
                            <input name="firma_tecnico" readonly value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}" class="form-control bg-light shadow-sm">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Autorización (Jefe)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                            <input id="firmaJefe" name="firma_jefe_area" readonly value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}" class="form-control bg-light shadow-sm">
                        </div>
                    </div>
                </div>

                {{-- OBSERVACIONES --}}
                <div class="mb-4">
                    <label class="form-label text-muted">Observaciones Adicionales</label>
                    <textarea name="observaciones" class="form-control shadow-sm border-light" rows="2" placeholder="Notas internas o aclaraciones finales"></textarea>
                </div>

                {{-- CAMPOS HIDDEN --}}
                <input type="hidden" name="id_servicio" value="{{ request('id_servicio') }}">
                <input type="hidden" name="id_ticket" value="{{ request('id_ticket') }}">

                {{-- BOTONES DE ACCIÓN --}}
                <div class="d-flex justify-content-end gap-2 border-top pt-4">
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary px-4 rounded-pill fw-bold">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-save me-1"></i> Guardar Formato
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Tipo servicio: otro */
    const tipoServicio = document.getElementById('tipo_servicio');
    const servicioOtro = document.getElementById('servicioOtro');

    tipoServicio.addEventListener('change', () => {
        if (tipoServicio.value === 'otro') {
            servicioOtro.style.display = 'block';
            servicioOtro.required = true;
            servicioOtro.focus();
        } else {
            servicioOtro.style.display = 'none';
            servicioOtro.required = false;
            servicioOtro.value = '';
        }
    });

    /* Autocompletar solicitante si Tipo Atención = Jefe */
    const tipoAtencion = document.getElementById('tipoAtencion');
    const firmaSolicitante = document.getElementById('firmaSolicitante');
    const firmaJefe = document.getElementById('firmaJefe');

    tipoAtencion.addEventListener('change', () => {
        if (tipoAtencion.value === 'Jefe') {
            firmaSolicitante.value = firmaJefe.value;
            firmaSolicitante.readOnly = true;
            firmaSolicitante.classList.add('bg-light');
        } else {
            firmaSolicitante.value = '';
            firmaSolicitante.readOnly = false;
            firmaSolicitante.classList.remove('bg-light');
        }
    });
});
</script>
@endsection