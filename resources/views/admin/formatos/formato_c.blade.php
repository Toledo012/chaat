@extends('layouts.admin')

@section('title', 'Formato C - Redes y Telefonía')
@section('header_title', 'Formato C - Redes / Telefonía')
@section('header_subtitle', 'Registro de mantenimiento, configuración e instalación de infraestructura')

@section('styles')
<style>
    .card-form { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .form-section-title { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #399e91; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px; margin-bottom: 20px; letter-spacing: 0.5px; }
    .form-label, label { font-size: 0.8rem; font-weight: 700; color: #495057; text-transform: uppercase; margin-bottom: 5px; }
    .form-control, .form-select { border-radius: 10px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.9rem; transition: all 0.2s; }
    .form-control:focus, .form-select:focus { border-color: #399e91; box-shadow: 0 0 0 0.25rem rgba(57, 158, 145, 0.1); }
    .badge-info-custom { background-color: #e0f2f1; color: #00796b; border-radius: 8px; padding: 12px 15px; font-size: 0.85rem; font-weight: 600; }
    .table-materiales thead { background-color: #f8f9fa; font-size: 0.75rem; text-transform: uppercase; }
</style>
@endsection

@section('content')
<div class="container-fluid px-2">

    {{-- AVISO INFORMATIVO --}}
    <div class="badge-info-custom mb-4 d-flex align-items-center shadow-sm">
        <i class="fas fa-network-wired me-3 fa-lg"></i>
        <span>Registro técnico de conectividad: Asegúrese de detallar el origen de la falla para el reporte estadístico.</span>
    </div>

    <div class="card card-form shadow-sm">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('admin.formatos.c.store') }}">
                @csrf

                {{-- SECCIÓN 1: DATOS GENERALES --}}
                <div class="form-section-title"><i class="fas fa-info-circle me-2"></i>Información General</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Departamento <span class="text-danger">*</span></label>
                        <select name="id_departamento" class="form-select shadow-sm @error('id_departamento') is-invalid @enderror" required>
                            <option value="">Seleccionar departamento</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}" @selected(old('id_departamento') == $dep->id_departamento)>
                                    {{ $dep->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipo de Infraestructura <span class="text-danger">*</span></label>
                        <select name="tipo_red" class="form-select shadow-sm" required>
                            <option value="">Seleccionar...</option>
                            <option value="Red">Red de Datos (LAN/WAN)</option>
                            <option value="Telefonía">Telefonía / Conmutador</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipo de Servicio <span class="text-danger">*</span></label>
                        <select name="tipo_servicio" class="form-select shadow-sm" required>
                            <option value="">Seleccionar...</option>
                            <option value="Preventivo">Mantenimiento Preventivo</option>
                            <option value="Correctivo">Mantenimiento Correctivo</option>
                            <option value="Configuracion">Configuración / Ajustes</option>
                        </select>
                    </div>
                </div>

                {{-- SECCIÓN 2: DESCRIPCIÓN Y DIAGNÓSTICO --}}
                <div class="form-section-title"><i class="fas fa-file-alt me-2"></i>Descripción del Reporte</div>
                <div class="mb-4">
                    <label class="form-label">Asunto del Servicio <span class="text-danger">*</span></label>
                    <input name="descripcion_servicio" class="form-control shadow-sm" placeholder="Ej. Instalación de cableado estructurado en site principal" required>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label class="form-label">Diagnóstico Inicial</label>
                        <textarea name="diagnostico" class="form-control shadow-sm" rows="3" placeholder="Estado actual del nodo o línea"></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Origen de la Falla</label>
                        <select name="origen_falla" class="form-select shadow-sm">
                            <option value="">Seleccionar...</option>
                            <option value="Desgaste natural">Desgaste natural / Antigüedad</option>
                            <option value="Mala operación">Mala operación / Daño Físico</option>
                            <option value="Clima">Factores Climáticos</option>
                            <option value="Otro">Otro (Especificar en descripción)</option>
                        </select>
                    </div>
                </div>

                {{-- SECCIÓN 3: TRABAJO REALIZADO --}}
                <div class="form-section-title"><i class="fas fa-tools me-2"></i>Bitácora de Trabajo</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-primary">Resumen de Trabajo</label>
                        <textarea name="trabajo_realizado" class="form-control shadow-sm" rows="4" placeholder="Acciones generales tomadas"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-success">Detalle Técnico Específico</label>
                        <textarea name="detalle_realizado" class="form-control shadow-sm border-success-subtle" rows="4" placeholder="Especificaciones de configuración, puertos, IPs, etc."></textarea>
                    </div>
                </div>

                {{-- SECCIÓN 4: MATERIALES --}}
                <div class="form-section-title"><i class="fas fa-boxes me-2"></i>Insumos y Componentes</div>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle" id="tablaMateriales">
                        <thead class="table-light">
                            <tr class="text-muted">
                                <th class="ps-3 py-2">Material / Refacción de Red</th>
                                <th width="150" class="text-center py-2">Cantidad</th>
                                <th width="80" class="text-center py-2">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-2 border-0 border-bottom">
                                    <select name="materiales[0][id_material]" class="form-select border-0 shadow-none">
                                        <option value="">— Seleccionar material del catálogo —</option>
                                        @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                                            <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2 border-0 border-bottom text-center">
                                    <input type="number" name="materiales[0][cantidad]" class="form-control border-0 text-center shadow-none" min="1" value="1">
                                </td>
                                <td class="text-center border-0 border-bottom">
                                    <button type="button" class="btn btn-link text-success p-0 agregar-material"><i class="fas fa-plus-circle fa-lg"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- OBSERVACIONES --}}
                <div class="mb-4">
                    <label class="text-muted">Observaciones Finales</label>
                    <textarea name="observaciones" class="form-control shadow-sm border-light" rows="2" placeholder="Notas adicionales sobre la instalación o configuración"></textarea>
                </div>

                {{-- SECCIÓN 5: FIRMAS --}}
                <div class="form-section-title"><i class="fas fa-signature me-2"></i>Validación y Cierre</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label>Firma Solicitante</label>
                        <input name="firma_usuario" class="form-control shadow-sm" placeholder="Nombre de quien recibe">
                    </div>
                    <div class="col-md-4">
                        <label>Técnico Responsable</label>
                        <input name="firma_tecnico" readonly class="form-control bg-light shadow-sm" value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}">
                    </div>
                    <div class="col-md-4">
                        <label>Jefe de Área (Vo.Bo.)</label>
                        <input name="firma_jefe_area" readonly class="form-control bg-light shadow-sm" value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}">
                    </div>
                </div>

                {{-- CAMPOS HIDDEN --}}
                <input type="hidden" name="id_servicio" value="{{ request('id_servicio') }}">
                <input type="hidden" name="id_ticket" value="{{ request('id_ticket') }}">

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end gap-2 border-top pt-4 text-end">
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary px-4 rounded-pill fw-bold">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-save me-1"></i> Guardar Formato C
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){

    // Colapsar sidebar para tener más espacio de trabajo
    const sidebar = document.getElementById('navigation');
    if (sidebar && !sidebar.classList.contains('collapsed')) {
        sidebar.classList.add('collapsed');
        localStorage.setItem('sidebarCollapsed', true);
    }

    // Lógica dinámica de materiales
    document.addEventListener('click', e => {
        if (e.target.closest('.agregar-material')) {
            const tbody = document.querySelector('#tablaMateriales tbody');
            const index = tbody.querySelectorAll('tr').length;

            const fila = `
                <tr>
                    <td class="p-2 border-0 border-bottom">
                        <select name="materiales[${index}][id_material]" class="form-select border-0 shadow-none">
                            <option value="">Seleccionar material...</option>
                            @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                                <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-2 border-0 border-bottom text-center">
                        <input type="number" name="materiales[${index}][cantidad]" class="form-control border-0 text-center shadow-none" min="1" value="1">
                    </td>
                    <td class="text-center border-0 border-bottom">
                        <button type="button" class="btn btn-link text-danger p-0 eliminar-material">
                            <i class="fas fa-minus-circle fa-lg"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', fila);
        }

        if (e.target.closest('.eliminar-material')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endsection