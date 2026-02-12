@extends('layouts.admin')

@section('title', 'Formato B - Equipos e Impresoras')
@section('header_title', 'Formato B - Equipos / Impresoras')
@section('header_subtitle', 'Registro de mantenimiento preventivo y correctivo de hardware')

@section('styles')
<style>
    .card-form { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .form-section-title { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #399e91; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px; margin-bottom: 20px; letter-spacing: 0.5px; }
    .form-label, label { font-size: 0.8rem; font-weight: 700; color: #495057; text-transform: uppercase; margin-bottom: 5px; }
    .form-control, .form-select { border-radius: 10px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.9rem; transition: all 0.2s; }
    .form-control:focus, .form-select:focus { border-color: #399e91; box-shadow: 0 0 0 0.25rem rgba(57, 158, 145, 0.1); }
    .badge-info-custom { background-color: #e0f2f1; color: #00796b; border-radius: 8px; padding: 12px 15px; font-size: 0.85rem; font-weight: 600; }
    .table-materiales thead { background-color: #f8f9fa; font-size: 0.75rem; text-transform: uppercase; }
    .bg-pc-section { background-color: #f8fbff; border: 1px solid #e3efff; }
</style>
@endsection

@section('content')
<div class="container-fluid px-2">

    {{-- AVISO INFORMATIVO --}}
    <div class="badge-info-custom mb-4 d-flex align-items-center shadow-sm">
        <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
        <span>Atención: Todos los campos marcados con (*) son obligatorios para la validez del registro de inventario.</span>
    </div>

    <div class="card card-form shadow-sm">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('admin.formatos.b.store') }}">
                @csrf

                {{-- SECCIÓN 1: IDENTIFICACIÓN GENERAL --}}
                <div class="form-section-title"><i class="fas fa-fingerprint me-2"></i>Información del Equipo</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Subtipo de equipo <span class="text-danger">*</span></label>
                        <select id="selectSubtipo" name="subtipo" class="form-select shadow-sm" required>
                            <option value="">Seleccionar...</option>
                            <option value="Computadora">Computadora / Laptop</option>
                            <option value="Impresora">Impresora / Multifuncional</option>
                            <option value="otro">Otro (Especificar)</option>
                        </select>
                        <input type="text" name="subtipo_otro" id="inputSubtipoOtro" class="form-control mt-2 shadow-sm border-warning" placeholder="Especifique el subtipo" style="display: none;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Departamento Responsable <span class="text-danger">*</span></label>
                        <select name="id_departamento" class="form-select shadow-sm @error('id_departamento') is-invalid @enderror" required>
                            <option value="">Selecciona un departamento</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label>Nombre del Equipo <span class="text-danger">*</span></label>
                        <input name="equipo" class="form-control shadow-sm" placeholder="Ej. PC-ADMIN-01" required>
                    </div>
                    <div class="col-md-4">
                        <label>Marca <span class="text-danger">*</span></label>
                        <input name="marca" class="form-control shadow-sm" placeholder="HP, Lenovo, Epson..." required>
                    </div>
                    <div class="col-md-4">
                        <label>Modelo <span class="text-danger">*</span></label>
                        <input name="modelo" class="form-control shadow-sm" required>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label>Número de Inventario <span class="text-danger">*</span></label>
                        <input name="numero_inventario" class="form-control shadow-sm fw-bold" placeholder="ID Interno" required>
                    </div>
                    <div class="col-md-6">
                        <label>Número de Serie <span class="text-danger">*</span></label>
                        <input name="numero_serie" class="form-control shadow-sm fw-bold" placeholder="S/N del Fabricante" required>
                    </div>
                </div>

                {{-- BLOQUE DINÁMICO COMPUTADORA --}}
                <div id="bloqueComputadora" style="display:none;" class="p-4 rounded-4 mb-4 bg-pc-section shadow-sm">
                    <div class="form-section-title border-primary-subtle text-primary"><i class="fas fa-microchip me-2"></i>Especificaciones Técnicas (PC)</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label>Procesador <span class="text-danger">*</span></label>
                            <input name="procesador" id="inputProcesador" class="form-control" placeholder="Ej. Core i5 12th Gen">
                        </div>
                        <div class="col-md-6">
                            <label>Memoria RAM <span class="text-danger">*</span></label>
                            <select name="ram" id="selectRam" class="form-select">
                                <option value="">Seleccionar</option>
                                <option>4 GB</option><option>8 GB</option><option>16 GB</option>
                                <option>32 GB</option><option>64 GB</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Almacenamiento (Disco duro) <span class="text-danger">*</span></label>
                            <select name="disco_duro" id="selectDisco" class="form-select">
                                <option value="">Seleccionar</option>
                                <option>HDD 500 GB</option><option>HDD 1 TB</option>
                                <option>SSD 240 GB</option><option>SSD 480 GB</option><option>SSD 1 TB</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Sistema Operativo <span class="text-danger">*</span></label>
                            <select name="sistema_operativo" id="selectSO" class="form-select">
                                <option value="">Seleccionar</option>
                                <option>Windows 10</option><option>Windows 11</option>
                                <option>Linux</option><option>MacOS</option><option>Otro</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: DETALLES DEL SERVICIO --}}
                <div class="form-section-title"><i class="fas fa-tools me-2"></i>Bitácora de Servicio</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label>Descripción General del Problema <span class="text-danger">*</span></label>
                        <textarea name="descripcion_servicio" class="form-control shadow-sm" rows="2" required placeholder="Motivo del mantenimiento"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label>Tipo de Mantenimiento <span class="text-danger">*</span></label>
                        <select name="tipo_servicio" class="form-select shadow-sm" required>
                            <option value="">Seleccionar</option>
                            <option>Preventivo</option>
                            <option>Correctivo</option>
                            <option>Instalación</option>
                            <option>Diagnóstico</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label>Diagnóstico Técnico <span class="text-danger">*</span></label>
                        <textarea name="diagnostico" class="form-control shadow-sm" rows="2" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Causa Probable de Falla <span class="text-danger">*</span></label>
                        <select name="origen_falla" class="form-select shadow-sm" required>
                            <option value="">Seleccionar</option>
                            <option>Desgaste natural</option>
                            <option>Mala operación</option>
                            <option>Falla de Software</option>
                            <option>Descarga Eléctrica</option>
                            <option>Otro</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label>Resumen de Trabajo <span class="text-danger">*</span></label>
                        <textarea name="trabajo_realizado" class="form-control shadow-sm mb-3" rows="2" required placeholder="Resumen"></textarea>
                        
                        <label class="form-label">Acciones Específicas Realizadas <span class="text-danger">*</span></label>
                        <textarea name="detalle_realizado" class="form-control shadow-sm" rows="3" required placeholder="Detalle técnico paso a paso"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Conclusión / Recomendaciones <span class="text-danger">*</span></label>
                        <textarea name="conclusion_servicio" class="form-control shadow-sm h-100" rows="6" required placeholder="Estado final del equipo"></textarea>
                    </div>
                </div>

                {{-- SECCIÓN 3: MATERIALES --}}
                <div class="form-section-title"><i class="fas fa-boxes me-2"></i>Insumos Utilizados</div>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-materiales align-middle" id="tablaMateriales">
                        <thead class="text-muted">
                            <tr>
                                <th class="ps-3">Material o Refacción</th>
                                <th width="180" class="text-center">Cantidad</th>
                                <th width="80" class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-2">
                                    <select name="materiales[0][id_material]" class="form-select border-0 shadow-none">
                                        <option value="">— Seleccionar material del catálogo —</option>
                                        @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                                            <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2 text-center">
                                    <input type="number" name="materiales[0][cantidad]" class="form-control border-0 text-center shadow-none" min="1">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link text-success p-0 agregar-material"><i class="fas fa-plus-circle fa-lg"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <label class="text-muted">Observaciones Adicionales</label>
                    <textarea name="observaciones" class="form-control shadow-sm border-light" rows="2" placeholder="Notas de inventario o garantía..."></textarea>
                </div>

                {{-- SECCIÓN 4: FIRMAS --}}
                <div class="form-section-title"><i class="fas fa-signature me-2"></i>Validación Oficial</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label>Firma Solicitante <span class="text-danger">*</span></label>
                        <input name="firma_usuario" class="form-control shadow-sm" required placeholder="Nombre completo">
                    </div>
                    <div class="col-md-4">
                        <label>Técnico Responsable</label>
                        <input name="firma_tecnico" readonly class="form-control bg-light shadow-sm" value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}">
                    </div>
                    <div class="col-md-4">
                        <label>Visto Bueno (Jefe)</label>
                        <input name="firma_jefe_area" readonly class="form-control bg-light shadow-sm" value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}">
                    </div>
                </div>

                <input type="hidden" name="id_servicio" value="{{ request('id_servicio') }}">
                <input type="hidden" name="id_ticket" value="{{ request('id_ticket') }}">

                <div class="d-flex justify-content-end gap-2 border-top pt-4">
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary px-4 rounded-pill fw-bold">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-save me-1"></i> Guardar Formato B
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
        const selectSubtipo = document.getElementById('selectSubtipo');
        const inputSubtipoOtro = document.getElementById('inputSubtipoOtro');
        const bloqueComputadora = document.getElementById('bloqueComputadora');
        const camposPC = ['inputProcesador', 'selectRam', 'selectDisco', 'selectSO'];

        selectSubtipo.addEventListener('change', function() {
            if (this.value === 'otro') {
                inputSubtipoOtro.style.display = 'block';
                inputSubtipoOtro.required = true;
                inputSubtipoOtro.focus();
            } else {
                inputSubtipoOtro.style.display = 'none';
                inputSubtipoOtro.required = false;
                inputSubtipoOtro.value = '';
            }

            if (this.value === 'Computadora') {
                $(bloqueComputadora).fadeIn(); // Usamos un pequeño efecto de entrada
                camposPC.forEach(id => document.getElementById(id).required = true);
            } else {
                $(bloqueComputadora).fadeOut();
                camposPC.forEach(id => {
                    const el = document.getElementById(id);
                    el.required = false;
                    el.value = '';
                });
            }
        });

        // Lógica de materiales dinámica
        document.addEventListener('click', e => {
            if(e.target.closest('.agregar-material')){
                const tbody = document.querySelector('#tablaMateriales tbody');
                const index = tbody.querySelectorAll('tr').length;
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td class="p-2">
                        <select name="materiales[${index}][id_material]" class="form-select border-0 shadow-none">
                            <option value="">Seleccionar material...</option>
                            @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                                <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-2 text-center"><input type="number" name="materiales[${index}][cantidad]" class="form-control border-0 text-center shadow-none" min="1"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-link text-danger p-0 eliminar-material"><i class="fas fa-minus-circle fa-lg"></i></button>
                    </td>`;
                tbody.appendChild(fila);
            }
            if(e.target.closest('.eliminar-material')){
                e.target.closest('tr').remove();
            }
        });
    });
</script>
@endsection