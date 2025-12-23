@extends('layouts.admin')

@section('title', 'Departamentos')
@section('header_title', 'Departamentos')
@section('header_subtitle', 'Administración de áreas y usuarios')

@section('content')
<div class="container-fluid">

    {{-- CARD CONTENEDORA --}}
    <div class="card shadow-sm border-0">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-building text-primary fa-lg"></i>
                <div>
                    <h5 class="mb-0">Departamentos</h5>
                    <small class="text-muted">
                        Gestión de áreas internas y usuarios asignados
                    </small>
                </div>
            </div>

            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCrearDepartamento">
                <i class="fa-solid fa-plus me-1"></i> Nuevo departamento
            </button>
        </div>

        {{-- BODY --}}
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>Departamento</th>
                            <th>Estado</th>
                            <th>Usuarios</th>
                            <th width="140" class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($departamentos as $dep)
                        <tr>
                            <td class="fw-semibold">
                                {{ $dep->id_departamento }}
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $dep->nombre }}</div>
                                <small class="text-muted">
                                    {{ $dep->descripcion ?? 'Sin descripción' }}
                                </small>
                            </td>

                            <td>
                                <span class="badge {{ $dep->activo ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                                    {{ $dep->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                    {{ $dep->usuarios_count }} usuarios
                                </span>

                                <button class="btn btn-sm btn-outline-info ms-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalUsuarios{{ $dep->id_departamento }}"
                                        title="Ver usuarios">
                                    <i class="fa-solid fa-users"></i>
                                </button>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditar{{ $dep->id_departamento }}"
                                        title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- MODAL EDITAR --}}
                        <div class="modal fade"
                             id="modalEditar{{ $dep->id_departamento }}"
                             tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fa-solid fa-pen me-2"></i>
                                            Editar Departamento
                                        </h5>
                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>

                                    <form method="POST"
                                          action="{{ route('admin.departamentos.update', $dep) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label">Nombre</label>
                                                <input type="text"
                                                       name="nombre"
                                                       value="{{ $dep->nombre }}"
                                                       class="form-control"
                                                       required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Descripción</label>
                                                <textarea name="descripcion"
                                                          class="form-control">{{ $dep->descripcion }}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Estado</label>
                                                <select name="activo" class="form-select">
                                                    <option value="1" {{ $dep->activo ? 'selected' : '' }}>
                                                        Activo
                                                    </option>
                                                    <option value="0" {{ !$dep->activo ? 'selected' : '' }}>
                                                        Inactivo
                                                    </option>
                                                </select>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary"
                                                    data-bs-dismiss="modal">
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

                        {{-- MODAL USUARIOS --}}
                        <div class="modal fade"
                             id="modalUsuarios{{ $dep->id_departamento }}"
                             tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fa-solid fa-users me-2"></i>
                                            Usuarios – {{ $dep->nombre }}
                                        </h5>
                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        @if($dep->usuarios->count())
                                            <ul class="list-group">
                                                @foreach($dep->usuarios as $u)
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <span>
                                                            {{ $u->nombre }}
                                                            <small class="text-muted">
                                                                ({{ $u->puesto }})
                                                            </small>
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted text-center mb-0">
                                                No hay usuarios asignados
                                            </p>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="5"
                                class="text-center text-muted py-4">
                                <i class="fa-solid fa-circle-info me-1"></i>
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

{{-- MODAL CREAR --}}
<div class="modal fade" id="modalCrearDepartamento" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-building me-2"></i>
                    Nuevo Departamento
                </h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <form method="POST"
                  action="{{ route('admin.departamentos.store') }}">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text"
                               name="nombre"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  class="form-control"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="activo" class="form-select">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-primary">
                        Guardar departamento
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
