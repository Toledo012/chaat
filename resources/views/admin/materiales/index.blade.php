@extends('layouts.admin')

@section('title', 'Materiales')
@section('header_title', 'Catálogo de Materiales')
@section('header_subtitle', 'Administración de insumos y consumibles')

@section('content')
<div class="container-fluid">

    {{-- CARD CONTENEDORA --}}
    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-box text-primary fa-lg"></i>
                <div>
                    <h5 class="mb-0">Materiales</h5>
                    <small class="text-muted">
                        Catálogo de materiales disponibles para servicios
                    </small>
                </div>
            </div>

            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCrear">
                <i class="fa-solid fa-plus me-1"></i> Añadir material
            </button>
        </div>

        {{-- BODY --}}
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="80">N°</th>
                            <th>Nombre</th>
                            <th>Unidad sugerida</th>
                            <th width="140" class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($materiales as $m)
                        <tr>
                            <td class="fw-semibold">{{ $m->id_material }}</td>

                            <td>
                                <div class="fw-semibold">{{ $m->nombre }}</div>
                                <small class="text-muted">Material registrado</small>
                            </td>

                            <td>
                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                    {{ $m->unidad_sugerida ?? '—' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar{{ $m->id_material }}"
                                    title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <form action="{{ route('admin.materiales.destroy', $m->id_material) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                            title="Eliminar"
                                            onclick="return confirm('¿Eliminar material?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- MODAL EDITAR (MISMA LÓGICA) --}}
                        <div class="modal fade" id="modalEditar{{ $m->id_material }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fa-solid fa-pen me-2"></i> Editar Material
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="{{ route('admin.materiales.update', $m->id_material) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nombre</label>
                                                <input type="text"
                                                       name="nombre"
                                                       value="{{ $m->nombre }}"
                                                       class="form-control">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Unidad sugerida</label>
                                                <input type="text"
                                                       name="unidad_sugerida"
                                                       value="{{ $m->unidad_sugerida }}"
                                                       class="form-control">
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">
                                                Cancelar
                                            </button>
                                            <button class="btn btn-primary">
                                                Guardar cambios
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                No hay materiales registrados
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- MODAL CREAR (MISMA LÓGICA) --}}
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-box me-2"></i> Añadir Material
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.materiales.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Unidad sugerida</label>
                        <select name="unidad_sugerida" id="unidadSelect" class="form-select">
                            <option value="">Seleccionar…</option>
                            <option value="pieza">Pieza</option>
                            <option value="metro">Metro</option>
                            <option value="litro">Litro</option>
                            <option value="rollo">Rollo</option>
                            <option value="caja">Caja</option>
                            <option value="otro">Otro…</option>
                        </select>
                    </div>

                    {{-- CAMPO OCULTO "OTRO" --}}
                    <input
                        type="text"
                        name="unidad_otro"
                        id="unidadOtro"
                        class="form-control mt-2"
                        placeholder="Escribe la unidad"
                        style="display: none;"
                    >

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-primary">
                        Guardar Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JS ORIGINAL (SIN TOCAR) --}}
<script>
document.getElementById('unidadSelect').addEventListener('change', function() {
    let otro = document.getElementById('unidadOtro');

    if (this.value === 'otro') {
        otro.style.display = 'block';
        otro.required = true;
    } else {
        otro.style.display = 'none';
        otro.required = false;
        otro.value = '';
    }
});
</script>
@endsection
