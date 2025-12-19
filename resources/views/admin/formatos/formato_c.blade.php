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

            {{-- ================= DATOS GENERALES ================= --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Departamento <span class="text-danger">*</span></label>
                    <select name="id_departamento"
                            class="form-select @error('id_departamento') is-invalid @enderror"
                            required>
                        <option value="">Seleccionar departamento</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id_departamento }}"
                                {{ old('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                {{ $dep->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_departamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tipo de Red <span class="text-danger">*</span></label>
                    <select name="tipo_red" class="form-select" required>
                        <option value="">Seleccionar</option>
                        <option value="Red">Red</option>
                        <option value="Telefonía">Telefonía</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tipo de Servicio <span class="text-danger">*</span></label>
                    <select name="tipo_servicio" class="form-select" required>
                        <option value="">Seleccionar</option>
                        <option value="Preventivo">Preventivo</option>
                        <option value="Correctivo">Correctivo</option>
                        <option value="Configuracion">Configuración</option>
                    </select>
                </div>
            </div>

            {{-- ================= DESCRIPCIÓN ================= --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Descripción del Servicio <span class="text-danger">*</span></label>
                    <input name="descripcion_servicio"
                           class="form-control"
                           placeholder="Ej. instalación de cableado estructurado"
                           required>
                </div>
            </div>

            {{-- ================= DIAGNÓSTICO ================= --}}
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

            {{-- ================= TRABAJO REALIZADO ================= --}}
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

            {{-- ================= MATERIALES ================= --}}
            <hr>
            <h6><i class="fas fa-cogs me-1"></i> Materiales Utilizados</h6>

            <table class="table table-bordered" id="tablaMateriales">
                <thead class="table-light">
                    <tr>
                        <th>Material</th>
                        <th width="120">Cantidad</th>
                        <th width="90">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="materiales[0][id_material]" class="form-select">
                                <option value="">Seleccionar material</option>
                                @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                                    <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="materiales[0][cantidad]" class="form-control" min="1" value="1">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-success btn-sm agregar-material">
                                <i class="fas fa-plus"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- ================= FIRMAS ================= --}}
            <hr>
            <div class="row mb-3">
                <div class="col-md-4">
                    <input name="firma_usuario" placeholder="Solicitante" class="form-control">
                </div>
                <div class="col-md-4">
                    <input name="firma_tecnico"
                           readonly
                           class="form-control"
                           value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}">
                </div>
                <div class="col-md-4">
                    <input name="firma_jefe_area"
                           readonly
                           class="form-control"
                           value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}">
                </div>
            </div>

            {{-- ================= OBSERVACIONES ================= --}}
            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2"></textarea>
            </div>

            {{-- ================= BOTONES ================= --}}
            <div class="text-end">
                <button class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Guardar
                </button>
                <a href="{{ route('admin.formatos.create') }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){

    // Colapsar sidebar
    const sidebar = document.getElementById('navigation');
    if (sidebar && !sidebar.classList.contains('collapsed')) {
        sidebar.classList.add('collapsed');
        localStorage.setItem('sidebarCollapsed', true);
    }

    // Materiales dinámicos
    document.addEventListener('click', e => {

        if (e.target.closest('.agregar-material')) {
            const tbody = document.querySelector('#tablaMateriales tbody');
            const index = tbody.querySelectorAll('tr').length;

            const fila = `
                <tr>
                    <td>
                        <select name="materiales[${index}][id_material]" class="form-select">
                            <option value="">Seleccionar material</option>
                            @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                                <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="materiales[${index}][cantidad]" class="form-control" min="1" value="1">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm eliminar-material">
                            <i class="fas fa-trash"></i>
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
