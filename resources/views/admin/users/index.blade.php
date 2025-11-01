@extends('layouts.admin')

{{-- ======= Configuración de Títulos ======= --}}
@section('title', 'Gestión de Usuarios - Sistema de Formatos')
@section('header_title', 'Gestión de Usuarios')
@section('header_subtitle', 'Control de roles, permisos y estado de las cuentas')

{{-- ======= Contenido Principal ======= --}}
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(!Auth::user()->isAdmin())
<div class="alert alert-info">
    <h6><i class="fas fa-info-circle me-2"></i>Modo de Vista Limitada</h6>
    <p class="mb-0">Solo puedes realizar acciones para las que tienes permisos específicos. Contacta al administrador si necesitas más acceso.</p>
</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-1">
            <i class="fas fa-users-cog me-2"></i>Gestión Completa de Usuarios
        </h2>
        <p class="text-muted mb-0">Control total sobre usuarios y permisos del sistema</p>
    </div>

    @if(Auth::user()->puedeCrearUsuarios())
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-user-plus me-1"></i>Crear Nuevo Usuario
        </button>
    @endif
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Usuarios Registrados
            <span class="badge bg-light text-dark ms-2">{{ $usuarios->count() }}</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Departamento</th>
                            <th>Puesto</th>
                            <th>Email</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Permisos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                        <tr
                            data-id="{{ $usuario->id_usuario }}"
                            data-nombre="{{ $usuario->nombre }}"
                            data-departamento="{{ $usuario->departamento }}"
                            data-puesto="{{ $usuario->puesto }}"
                            data-email="{{ $usuario->email }}"
                            data-username="{{ optional($usuario->cuenta)->username }}"
                            data-has-account="{{ $usuario->cuenta ? '1' : '0' }}"
                            data-permisos='@json($usuario->cuenta->permisosArray() ?? [])'
                        >
                            {{-- === (contenido de tu tabla original, sin cambios) === --}}
                            {{-- copiado tal cual de tu versión anterior --}}
                            <td><strong>{{ $usuario->id_usuario }}</strong></td>
                            <td>
                                <strong>{{ $usuario->nombre }}</strong>
                                @if($usuario->id_usuario == Auth::user()->id_usuario)
                                    <span class="badge bg-info ms-1">Tú</span>
                                @endif
                            </td>
                            <td>{{ $usuario->departamento }}</td>
                            <td>{{ $usuario->puesto }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @if($usuario->cuenta)
                                    <span class="badge bg-primary">{{ $usuario->cuenta->username }}</span>
                                @else
                                    <span class="badge bg-secondary">Sin cuenta</span>
                                @endif
                            </td>
                            <td>
                                @if($usuario->cuenta)
                                    @if(Auth::user()->puedeCambiarRoles())
                                        <form action="{{ route('admin.users.update-role', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="rol" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="1" {{ $usuario->cuenta->id_rol == 1 ? 'selected' : '' }}>Administrador</option>
                                                <option value="2" {{ $usuario->cuenta->id_rol == 2 ? 'selected' : '' }}>Usuario</option>
                                            </select>
                                        </form>
                                    @else
                                        <span class="badge {{ $usuario->cuenta->id_rol == 1 ? 'bg-danger' : 'bg-primary' }}">
                                            {{ $usuario->cuenta->id_rol == 1 ? 'Admin' : 'Usuario' }}
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-warning">Sin rol</span>
                                @endif
                            </td>
                            <td>
                                @if($usuario->cuenta && Auth::user()->puedeCambiarRoles())
                                    <button type="button" class="btn btn-outline-info btn-sm btn-permisos">
                                        <i class="fas fa-cog me-1"></i>Permisos
                                    </button>
                                @elseif($usuario->cuenta)
                                    <small>
                                        @foreach($usuario->cuenta->permisosNombres() as $permiso)
                                            <span class="badge bg-info d-block mb-1">{{ $permiso }}</span>
                                        @endforeach
                                    </small>
                                @else
                                    <span class="badge bg-warning">Sin cuenta</span>
                                @endif
                            </td>
                            <td>
                                @if($usuario->cuenta)
                                    @if(Auth::user()->puedeActivarCuentas())
                                        <form action="{{ route('admin.users.toggle-status', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            @if($usuario->cuenta->estado == 'activo')
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check me-1"></i>Activo
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-times me-1"></i>Inactivo
                                                </button>
                                            @endif
                                        </form>
                                    @else
                                        <span class="badge {{ $usuario->cuenta->estado == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $usuario->cuenta->estado == 'activo' ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-danger">Sin cuenta</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if($usuario->cuenta)
                                        @if(Auth::user()->puedeEditarUsuarios())
                                            <button type="button" class="btn btn-outline-primary btn-sm btn-edit">
                                                <i class="fas fa-edit me-1"></i>Editar
                                            </button>
                                        @endif
                                        @if(Auth::user()->puedeEliminarUsuarios() && $usuario->id_usuario != Auth::user()->id_usuario)
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-delete">
                                                <i class="fas fa-trash me-1"></i>Eliminar
                                            </button>
                                        @endif
                                    @else
                                        @if(Auth::user()->puedeCrearUsuarios())
                                            <form action="{{ route('admin.users.create-account', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-user-plus me-1"></i>Crear Cuenta
                                                </button>
                                            </form>
                                        @endif
                                        @if(Auth::user()->puedeEliminarUsuarios())
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-delete">
                                                <i class="fas fa-trash me-1"></i>Eliminar
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- ======= Scripts ======= --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '{{ url("admin/users") }}';

    // Limpieza de backdrop residual
    document.addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
    });

    // --- Editar Usuario ---
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const tr = this.closest('tr');
            const id = tr.dataset.id;
            const nombre = tr.dataset.nombre || '';
            const departamento = tr.dataset.departamento || '';
            const puesto = tr.dataset.puesto || '';
            const email = tr.dataset.email || '';
            const username = tr.dataset.username || '';

            const contenido = `
                <form action="${baseUrl}/${id}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Usuario: ${nombre}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control mb-2" name="nombre" value="${nombre}">
                        <label class="form-label">Departamento</label>
                        <input type="text" class="form-control mb-2" name="departamento" value="${departamento}">
                        <label class="form-label">Puesto</label>
                        <input type="text" class="form-control mb-2" name="puesto" value="${puesto}">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control mb-2" name="email" value="${email}">
                        <label class="form-label">Usuario</label>
                        <input type="text" class="form-control" name="username" value="${username}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar
                        </button>
                    </div>
                </form>`;
            document.getElementById('editModalContent').innerHTML = contenido;
            new bootstrap.Modal(document.getElementById('globalEditModal')).show();
        });
    });

    // --- Permisos ---
    document.querySelectorAll('.btn-permisos').forEach(btn => {
        btn.addEventListener('click', function() {
            const tr = this.closest('tr');
            const id = tr.dataset.id;
            const nombre = tr.dataset.nombre || '';
            const permisos = JSON.parse(tr.dataset.permisos || '[]');
            const checked = val => permisos.includes(val) ? 'checked' : '';

            const contenido = `
                <form action="${baseUrl}/${id}/update-permissions" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h6 class="modal-title"><i class="fas fa-cog me-2"></i>Permisos: ${nombre}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permisos[]" value="1" ${checked(1)}>
                            <label class="form-check-label">Ver Usuarios</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permisos[]" value="2" ${checked(2)}>
                            <label class="form-check-label">Gestionar Formatos</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                    </div>
                </form>`;
            document.getElementById('permisosModalContent').innerHTML = contenido;
            new bootstrap.Modal(document.getElementById('globalPermisosModal')).show();
        });
    });

    // --- Eliminar ---
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const tr = this.closest('tr');
            const id = tr.dataset.id;
            const nombre = tr.dataset.nombre || '';
            const contenido = `
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Eliminar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Eliminar a <strong>${nombre}</strong>? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="${baseUrl}/${id}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Eliminar
                        </button>
                    </form>
                </div>`;
            document.getElementById('deleteModalContent').innerHTML = contenido;
            new bootstrap.Modal(document.getElementById('globalDeleteModal')).show();
        });

    });
});
</script>
@endsection
