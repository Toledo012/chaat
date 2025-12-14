@extends('layouts.admin')

@section('header_title', 'Departamentos')
@section('header_subtitle', 'Administración de áreas del sistema')

@section('content')
<div class="container-fluid">

    {{-- CARD CONTENEDORA --}}
    <div class="card shadow-sm border-0">

        {{-- HEADER DEL CARD --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-building fa-lg text-primary"></i>
                <div>
                    <h5 class="mb-0">Catálogo de Departamentos</h5>
                    <small class="text-muted">
                        Gestiona las áreas que pueden generar servicios y tickets
                    </small>
                </div>
            </div>

            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCreate">
                <i class="fas fa-plus me-1"></i> Nuevo
            </button>
        </div>

        {{-- BODY --}}
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Departamento</th>
                            <th>Descripción</th>
                            <th width="120">Estado</th>
                            <th width="120" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departamentos as $dep)
                            <tr>
                                <td class="fw-semibold">{{ $dep->id_departamento }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $dep->nombre }}</div>
                                    <small class="text-muted">
                                        {{ $dep->usuarios->count() ?? 0 }} usuarios asociados
                                    </small>
                                </td>

                                <td class="text-muted">
                                    {{ $dep->descripcion ?? 'Sin descripción' }}
                                </td>

                                <td>
                                    @if($dep->activo)
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> Activo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                            <i class="fas fa-ban me-1"></i> Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit"
                                        data-id="{{ $dep->id_departamento }}"
                                        data-nombre="{{ $dep->nombre }}"
                                        data-descripcion="{{ $dep->descripcion }}"
                                        data-activo="{{ $dep->activo }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-1"></i>
                                    No hay departamentos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- ================= MODAL CREAR ================= --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.departamentos.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-building me-2"></i> Nuevo Departamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" checked>
                        <label class="form-check-label">Departamento activo</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-primary">
                        Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL EDITAR ================= --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formEdit">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i> Editar Departamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" id="editNombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="editDescripcion" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="editActivo">
                        <label class="form-check-label">Departamento activo</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-primary">
                        Actualizar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const modalEdit = document.getElementById('modalEdit');

modalEdit.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;

    document.getElementById('formEdit').action =
        `/admin/departamentos/${button.dataset.id}`;

    document.getElementById('editNombre').value = button.dataset.nombre;
    document.getElementById('editDescripcion').value = button.dataset.descripcion ?? '';
    document.getElementById('editActivo').checked = button.dataset.activo == 1;
});
</script>
@endpush
