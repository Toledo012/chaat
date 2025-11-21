@extends('layouts.admin')

{{-- ======= CONFIGURACIÓN DE TÍTULOS ======= --}}
@section('title', 'Gestión de Usuarios - Sistema de Formatos')
@section('header_title', 'Gestión de Usuarios')
@section('header_subtitle', 'Control de roles, permisos y estado de las cuentas')

{{-- ======= ESTILOS ESPECÍFICOS ======= --}}
@section('styles')
<style>
    .content-wrapper {
        margin-left: 260px;
        padding: 2rem;
        transition: margin-left 0.3s ease;
        animation: fadeInUp 0.5s ease-out;
    }
    .sidebar.collapsed + .content-wrapper {
        margin-left: 80px;
    }
    .card-header {
        background-color: #399e91;
        color: white;
        font-weight: 600;
    }
    .theme-toggle {
        border: none;
        background: transparent;
        color: white;
        cursor: pointer;
        font-size: 1.2rem;
    }
    .theme-toggle:hover {
        transform: scale(1.1);
    }
</style>
@endsection

{{-- ======= CONTENIDO PRINCIPAL ======= --}}
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

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Usuarios Registrados
            <span class="badge bg-secondary ms-2">{{ $usuarios->count() }}</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-container">
            <div class="table-responsive">
                {{-- ======= TABLA ORIGINAL SIN CAMBIOS ======= --}}
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
                                            <span class="badge bg-info permisos-badge d-block mb-1">{{ $permiso }}</span>
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
                {{-- ======= FIN DE TABLA ======= --}}
            </div>
        </div>
    </div>
</div>

{{-- ======= MODALES GLOBALES ======= --}}
<div class="modal fade" id="globalEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="editModalContent"></div>
    </div>
</div>

<div class="modal fade" id="globalPermisosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="permisosModalContent"></div>
    </div>
</div>

<div class="modal fade" id="globalDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="deleteModalContent"></div>
    </div>
</div>

@if(Auth::user()->puedeCrearUsuarios())
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Crear Nuevo Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Nombre Completo *</label><input type="text" class="form-control" name="nombre" required></div>

                                            <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Departamento</label>
                                <select name="departamento" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option>Sistemas</option>
                            
                                </select>
                            </div>

                                                    <div class="col-md-6">
                                <label>Puesto</label>
                                <select name="puesto" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option>Jefe de Area</option>
                                    <option>Técnico</option>
                                    <option>Programador</option>

                                </select>
                            </div>
                        <div class="mb-3"><label class="form-label">Extensión</label><input type="text" class="form-control" name="extension"></div>
                        <div class="mb-3"><label class="form-label">Email *</label><input type="text" class="form-control" name="username" required></div>
                        <div class="mb-3"><label class="form-label">Confirmar Email*</label><input type="text" class="form-control" name="email" required></div>

                        <div class="mb-3"><label class="form-label">Contraseña *</label><input type="password" class="form-control" name="password" required></div>
                        <div class="mb-3"><label class="form-label">Rol *</label>
                            <select name="rol" class="form-select" required>
                                <option value="2">Usuario Normal</option>
                                <option value="1">Administrador</option>
                            </select>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

{{-- ======= SCRIPTS ======= --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '{{ url("admin/users") }}';

    // Limpieza de modales
    document.addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
    });

    // --- Editar usuario ---
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
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Usuario: ${nombre}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Nombre</label><input type="text" class="form-control" name="nombre" value="${nombre}" required></div>
                        <div class="mb-3"><label class="form-label">Departamento</label><input type="text" class="form-control" name="departamento" value="${departamento}"></div>
                        <div class="mb-3"><label class="form-label">Puesto</label><input type="text" class="form-control" name="puesto" value="${puesto}"></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="${email}"></div>
                        <div class="mb-3"><label class="form-label">Usuario</label><input type="text" class="form-control" name="username" value="${username}"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar Cambios</button>
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
            let permisos = [];
            try { permisos = JSON.parse(tr.dataset.permisos || '[]'); } catch(e) {}

            const checked = (val) => permisos.includes(val) ? 'checked' : '';
            const contenido = `
                <form action="${baseUrl}/${id}/update-permissions" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h6 class="modal-title"><i class="fas fa-cog me-2"></i>Permisos: ${nombre}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="permisos[]" value="1" ${checked(1)}> <label class="form-check-label">Ver Usuarios</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="permisos[]" value="2" ${checked(2)}> <label class="form-check-label">Gestionar Formatos</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="permisos[]" value="3" ${checked(3)}> <label class="form-check-label">Crear Usuarios</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="permisos[]" value="4" ${checked(4)}> <label class="form-check-label">Editar Usuarios</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="permisos[]" value="5" ${checked(5)}> <label class="form-check-label">Eliminar Usuarios</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="permisos[]" value="6" ${checked(6)}> <label class="form-check-label">Cambiar Roles</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="permisos[]" value="7" ${checked(7)}> <label class="form-check-label">Activar Cuentas</label></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm">Guardar Permisos</button>
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
            const hasAccount = tr.dataset.hasAccount === '1';
            const mensajeCuenta = hasAccount ? '<p>Se eliminará también su cuenta asociada.</p>' : '';
            const contenido = `
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Deseas eliminar al usuario <strong>${nombre}</strong>?</p>
                    ${mensajeCuenta}
                    <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="${baseUrl}/${id}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Eliminar</button>
                    </form>
                </div>`;
            document.getElementById('deleteModalContent').innerHTML = contenido;
            new bootstrap.Modal(document.getElementById('globalDeleteModal')).show();
        });
    });
});
</script>
@endsection
