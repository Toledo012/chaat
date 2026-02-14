@extends('layouts.admin')

@section('title', 'Formato B - Equipos e Impresoras')
@section('header_title', 'Formato B - Equipos / Impresoras')
@section('header_subtitle', 'Registro de mantenimiento de equipos e impresoras')

@section('styles')
    <style>
        .card-header { background-color: #399e91; color: white; font-weight: 600; }
        .form-control, .form-select { border-radius: 8px; }
        .btn-primary { background-color: #399e91; border-color: #399e91; }
        .btn-primary:hover { background-color: #2f847a; border-color: #2f847a; }
        .alert-info { background-color: #d1f0eb; border-color: #399e91; color: #25685d; font-weight: 500; }
        label { font-weight: 500; margin-bottom: 0.3rem; }
    </style>
@endsection

@section('content')

    <div class="alert alert-info mb-4 d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-2"></i>
        Todos los campos son obligatorios para garantizar un registro completo.
    </div>

    <div class="card shadow border-0">
        <div class="card-header"><i class="fas fa-desktop me-2"></i>Formulario de Registro - Formato B</div>
        <div class="card-body">

            <form method="POST" action="{{ route('admin.formatos.b.store') }}">
                @csrf

                {{-- SECCIÓN: DATOS DE CABECERA --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Subtipo de equipo <span class="text-danger">*</span></label>
                        <select id="selectSubtipo" name="subtipo" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="Computadora">Computadora</option>
                            <option value="Impresora">Impresora</option>
                            <option value="otro">Otro…</option>
                        </select>
                        
                        <input type="text" name="subtipo_otro" id="inputSubtipoOtro" 
                               class="form-control mt-2" placeholder="Especifique el subtipo" style="display: none;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Departamento <span class="text-danger">*</span></label>
                        <select name="id_departamento" class="form-select @error('id_departamento') is-invalid @enderror" required>
                            <option value="">Selecciona un departamento</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-1"></i> Información del Equipo</h6>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Nombre del Equipo <span class="text-danger">*</span></label>
                        <input name="equipo" class="form-control" placeholder="Ej. PC-ADMIN-01" required>
                    </div>
                    <div class="col-md-4">
                        <label>Marca <span class="text-danger">*</span></label>
                        <input name="marca" class="form-control" placeholder="HP, Lenovo, etc." required>
                    </div>
                    <div class="col-md-4">
                        <label>Modelo <span class="text-danger">*</span></label>
                        <input name="modelo" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Número de Inventario <span class="text-danger">*</span></label>
                        <input name="numero_inventario" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Número de Serie <span class="text-danger">*</span></label>
                        <input name="numero_serie" class="form-control" required>
                    </div>
                </div>

                {{-- BLOQUE DINÁMICO COMPUTADORA --}}
                <div id="bloqueComputadora" style="display:none;" class="p-3 bg-light rounded mb-3">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Procesador <span class="text-danger">*</span></label>
                            <input name="procesador" id="inputProcesador" class="form-control">
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
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Disco duro <span class="text-danger">*</span></label>
                            <select name="disco_duro" id="selectDisco" class="form-select">
                                <option value="">Seleccionar</option>
                                <option>HDD 500 GB</option><option>HDD 1 TB</option>
                                <option>SSD 240 GB</option><option>SSD 480 GB</option><option>SSD 1 TB</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Sistema operativo <span class="text-danger">*</span></label>
                            <select name="sistema_operativo" id="selectSO" class="form-select">
                                <option value="">Seleccionar</option>
                                <option>Windows 10</option><option>Windows 11</option>
                                <option>Linux</option><option>MacOS</option><option>Otro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-tools me-1"></i> Detalles del Servicio</h6>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Descripción del servicio <span class="text-danger">*</span></label>
                        <textarea name="descripcion_servicio" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Tipo de servicio <span class="text-danger">*</span></label>
                        <select name="tipo_servicio" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option>Preventivo</option>
                            <option>Correctivo</option>
                            <option>Instalación</option>
                            <option>Corrección</option>
                            <option>Diagnóstico</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Diagnóstico <span class="text-danger">*</span></label>
                        <textarea name="diagnostico" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Origen de la falla <span class="text-danger">*</span></label>
                        <select name="origen_falla" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option>Desgaste natural</option>
                            <option>Mala operación</option>
                            <option>Otro</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Trabajo realizado <span class="text-danger">*</span></label>
                        <textarea name="trabajo_realizado" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Conclusión del servicio <span class="text-danger">*</span></label>
                        <textarea name="conclusion_servicio" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Trabajo Específico Realizado <span class="text-danger">*</span></label>
                    <textarea name="detalle_realizado" class="form-control" rows="3" required></textarea>
                </div>

                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-boxes me-1"></i> Materiales y Observaciones</h6>

                <table class="table table-bordered mb-4" id="tablaMateriales">
                    <thead class="table-light">
                        <tr><th>Material</th><th width="150">Cantidad</th><th width="100">Acción</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="materiales[0][id_material]" class="form-select">
                                    <option value="">Sin material / Seleccionar</option>
                                    @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                                        <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="materiales[0][cantidad]" class="form-control" min="1"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-success btn-sm agregar-material"><i class="fas fa-plus"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="mb-4">
                    <label>Observaciones adicionales</label>
                    <textarea name="observaciones" class="form-control" rows="2" placeholder="Opcional..."></textarea>
                </div>

                <hr>
                <h6 class="text-primary mb-3"><i class="fas fa-signature me-1"></i> Validación y Firmas</h6>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label>Firma solicitante <span class="text-danger">*</span></label>
                        <input name="firma_usuario" class="form-control" required placeholder="Nombre de quien recibe">
                    </div>
                    <div class="col-md-4">
                        <label>Técnico responsable</label>
                        <input name="firma_tecnico" readonly class="form-control bg-light" value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}">
                    </div>
                    <div class="col-md-4">
                        <label>Jefe de Área</label>
                        <input name="firma_jefe_area" readonly class="form-control bg-light" value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}">
                    </div>
                </div>

                <input type="hidden" name="id_servicio" value="{{ request('id_servicio') }}">
                <input type="hidden" name="id_ticket" value="{{ request('id_ticket') }}">

                <div class="text-end">
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save me-2"></i>Guardar Formato B</button>
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary shadow-sm">Cancelar</a>
                </div>
            </form>
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
                } else {
                    inputSubtipoOtro.style.display = 'none';
                    inputSubtipoOtro.required = false;
                    inputSubtipoOtro.value = '';
                }

                if (this.value === 'Computadora') {
                    bloqueComputadora.style.display = 'block';
                    camposPC.forEach(id => document.getElementById(id).required = true);
                } else {
                    bloqueComputadora.style.display = 'none';
                    camposPC.forEach(id => {
                        const el = document.getElementById(id);
                        el.required = false;
                        el.value = '';
                    });
                }
            });

            document.addEventListener('click', e => {
                if(e.target.closest('.agregar-material')){
                    const tbody = document.querySelector('#tablaMateriales tbody');
                    const index = tbody.querySelectorAll('tr').length;
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td>
                            <select name="materiales[${index}][id_material]" class="form-select">
                                <option value="">Sin material / Seleccionar</option>
                                @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                                    <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="materiales[${index}][cantidad]" class="form-control" min="1"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm eliminar-material"><i class="fas fa-trash"></i></button>
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