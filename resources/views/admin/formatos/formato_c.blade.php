@extends('layouts.admin')

@section('title', 'Formato C - Redes y Telefonía')
@section('header_title', 'Formato C - Redes y Telefonía')
@section('header_subtitle', 'Registro de mantenimiento e instalación de redes')

@section('styles')
    <style>
        .card-header { background-color: #399e91; color: white; font-weight: 600; }
        .form-control, .form-select { border-radius: 8px; }
        .btn-primary { background-color: #399e91; border-color: #399e91; }
        .btn-primary:hover { background-color: #2f847a; border-color: #2f847a; }
        .alert-info { background-color: #d1f0eb; border-color: #399e91; color: #25685d; font-weight: 500; }
    </style>
@endsection

@section('content')
    <div class="alert alert-info mb-4 d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-2"></i>
        Todos los campos marcados son obligatorios antes de guardar el formato.
    </div>

    <div class="card shadow border-0">
        <div class="card-header">
            <i class="fas fa-network-wired me-2"></i> Formulario de Formato C
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.formatos.c.store') }}">
                @csrf

                <div class="row mb-4">

                    {{-- DEPARTAMENTO --}}
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">
                            Departamento <span class="text-danger">*</span>
                        </label>
                        @php $selectedDept = $ticketDeptId ?? old('id_departamento'); @endphp
                        @if($ticketDeptId)
                            <select name="_depto_display" class="form-select @error('id_departamento') is-invalid @enderror" disabled>
                                @foreach($departamentos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE) as $dep)
                                    <option value="{{ $dep->id_departamento }}" {{ $selectedDept == $dep->id_departamento ? 'selected' : '' }}>{{ $dep->nombre }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="id_departamento" value="{{ $ticketDeptId }}">
                        @else
                            <div class="input-group">
                                <select id="selectDepartamentoFormatoC" name="id_departamento"
                                        class="form-select shadow-sm @error('id_departamento') is-invalid @enderror" required>
                                    <option value="">Selecciona un departamento</option>
                                    @foreach($departamentos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE) as $dep)
                                        <option value="{{ $dep->id_departamento }}" {{ old('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>{{ $dep->nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-success fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#modalCrearDepartamentoFormatoC">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Si no existe, crea uno aquí mismo.</small>
                        @endif
                        @error('id_departamento')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tipo de Red <span class="text-danger">*</span></label>
                        <select name="tipo_red" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="Red">Red</option>
                            <option value="Telefonía">Telefonía</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tipo de Servicio <span class="text-danger">*</span></label>
                        <select name="tipo_servicio" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="Preventivo">Preventivo</option>
                            <option value="Correctivo">Correctivo</option>
                            <option value="Configuracion">Configuración</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción del Servicio <span class="text-danger">*</span></label>
                    <input name="descripcion_servicio" class="form-control" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Diagnóstico</label>
                        <textarea name="diagnostico" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Origen de la Falla</label>
                        <select name="origen_falla" class="form-select">
                            <option value="">Seleccionar</option>
                            <option value="Desgaste natural">Desgaste natural</option>
                            <option value="Mala operación">Mala operación</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Trabajo Realizado</label>
                        <textarea name="trabajo_realizado" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Detalle del Servicio</label>
                        <textarea name="detalle_realizado" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <hr>

                {{-- Encabezado materiales con botón Nuevo Material --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Materiales Utilizados</h6>
                    <button type="button" class="btn btn-outline-success btn-sm fw-bold"
                            data-bs-toggle="modal" data-bs-target="#modalCrearMaterialC">
                        <i class="fas fa-plus me-1"></i> Nuevo Material
                    </button>
                </div>

                <table class="table table-bordered mb-4" id="tablaMateriales">
                    <thead class="table-light">
                    <tr><th>Material</th><th width="120">Cantidad</th><th width="90">Acción</th></tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <select name="materiales[0][id_material]" class="form-select select-material">
                                <option value="">Seleccionar material</option>
                                @foreach(\DB::table('catalogo_materiales')->get() as $mat)
                                    <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="materiales[0][cantidad]" class="form-control" min="1" value="1"></td>
                        <td class="text-center"><button type="button" class="btn btn-outline-success btn-sm agregar-material"><i class="fas fa-plus"></i></button></td>
                    </tr>
                    </tbody>
                </table>

                <div class="row mb-3">
                    <div class="col-md-4"><input name="firma_usuario" placeholder="Solicitante" class="form-control"></div>
                    <div class="col-md-4"><input name="firma_tecnico" readonly class="form-control" value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}"></div>
                    <div class="col-md-4"><input name="firma_jefe_area" readonly class="form-control" value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}"></div>
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

    {{-- MODAL CREAR DEPARTAMENTO --}}
    @if(!$ticketDeptId)
        <div class="modal fade" id="modalCrearDepartamentoFormatoC" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white border-0">
                        <h5 class="modal-title fw-bold"><i class="fas fa-building me-2"></i> Registrar Departamento</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formCrearDepartamentoFormatoC">
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
                                <input class="form-check-input" type="checkbox" value="1" id="activoDeptoFormatoC" name="activo" checked>
                                <label class="form-check-label" for="activoDeptoFormatoC">Activo</label>
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

    {{-- MODAL CREAR MATERIAL --}}
    <div class="modal fade" id="modalCrearMaterialC" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-box me-2"></i> Registrar Material</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formCrearMaterialC">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre *</label>
                            <input type="text" name="nombre" class="form-control shadow-sm" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Unidad sugerida</label>
                            <select name="unidad_sugerida" id="selectUnidadC" class="form-select shadow-sm">
                                <option value="">Sin unidad</option>
                                <option value="pza">Pieza</option>
                                <option value="mts">Metros</option>
                                <option value="caja">Caja</option>
                                <option value="lt">Litro</option>
                                <option value="otro">Otro…</option>
                            </select>
                        </div>
                        <div class="mb-3" id="unidadOtroCDiv" style="display:none;">
                            <label class="form-label small fw-bold text-muted text-uppercase">Especificar unidad</label>
                            <input type="text" name="unidad_otro" id="unidadOtroC" class="form-control shadow-sm" maxlength="20">
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

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(){

            // ----- Unidad "otro" en modal material -----
            document.getElementById('selectUnidadC').addEventListener('change', function() {
                const show = this.value === 'otro';
                document.getElementById('unidadOtroCDiv').style.display = show ? 'block' : 'none';
                const inp = document.getElementById('unidadOtroC');
                inp.required = show;
                if (!show) inp.value = '';
            });

            // ----- Filas de materiales -----
            let opcionesMateriales = document.querySelector('#tablaMateriales .select-material').innerHTML;

            document.addEventListener('click', e => {
                if (e.target.closest('.agregar-material')) {
                    const tbody = document.querySelector('#tablaMateriales tbody');
                    const index = tbody.querySelectorAll('tr').length;
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                <td><select name="materiales[${index}][id_material]" class="form-select select-material">${opcionesMateriales}</select></td>
                <td><input type="number" name="materiales[${index}][cantidad]" class="form-control" min="1" value="1"></td>
                <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm eliminar-material"><i class="fas fa-trash"></i></button></td>`;
                    tbody.appendChild(fila);
                }
                if (e.target.closest('.eliminar-material')) e.target.closest('tr').remove();
            });

            // ----- quickStore Departamento -----
            const formDepto = document.getElementById('formCrearDepartamentoFormatoC');
            if (formDepto) {
                formDepto.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const btn = formDepto.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    try {
                        const res = await fetch("{{ route('admin.departamentos.quickStore') }}", {
                            method: "POST",
                            headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                            body: new FormData(formDepto)
                        });
                        const data = await res.json();
                        if (!res.ok) { alert(data?.message || 'Error al crear departamento'); return; }
                        const sel = document.getElementById('selectDepartamentoFormatoC');
                        const opt = document.createElement('option');
                        opt.value = data.id_departamento; opt.textContent = data.nombre; opt.selected = true;
                        sel.appendChild(opt);
                        bootstrap.Modal.getInstance(document.getElementById('modalCrearDepartamentoFormatoC'))?.hide();
                        formDepto.reset();
                        document.getElementById('activoDeptoFormatoC').checked = true;
                    } catch (err) { console.error(err); alert('No se pudo crear el departamento.'); }
                    finally { btn.disabled = false; }
                });
            }

            // ----- quickStore Material -----
            const formMaterial = document.getElementById('formCrearMaterialC');
            formMaterial.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = formMaterial.querySelector('button[type="submit"]');
                btn.disabled = true;
                try {
                    const res = await fetch("{{ route('admin.materiales.quickStore') }}", {
                        method: "POST",
                        headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                        body: new FormData(formMaterial)
                    });
                    const data = await res.json();
                    if (!res.ok) { alert(data?.message || 'Error al crear material'); return; }

                    // Agregar opción a todos los selects existentes
                    document.querySelectorAll('#tablaMateriales .select-material').forEach(sel => {
                        const opt = document.createElement('option');
                        opt.value = data.id_material; opt.textContent = data.nombre;
                        sel.appendChild(opt);
                    });
                    // Actualizar template para filas nuevas
                    opcionesMateriales = document.querySelector('#tablaMateriales .select-material').innerHTML;

                    bootstrap.Modal.getInstance(document.getElementById('modalCrearMaterialC'))?.hide();
                    formMaterial.reset();
                    document.getElementById('selectUnidadC').dispatchEvent(new Event('change'));
                } catch (err) { console.error(err); alert('No se pudo crear el material.'); }
                finally { btn.disabled = false; }
            });

        });
    </script>
@endsection
