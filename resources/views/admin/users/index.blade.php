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
                          data-rol="{{ optional($usuario->cuenta)->id_rol }}"
                          data-es-super="{{ $usuario->id_usuario == 1 ? '1' : '0' }}"
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

                                {{-- IDENTIFICADOR SUPER ADMIN --}}
                                @if($usuario->id_usuario == 1)
                                    <span class="badge bg-danger ms-1">Super Admin</span>
                                @endif

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

                            {{-- ========================= --}}
                            {{--   COLUMNA: ROL           --}}
                            {{-- ========================= --}}
                            <td>
                                @if($usuario->cuenta)

                                    {{-- ⚠️ Solo SUPER ADMIN puede cambiar rol de admins --}}
                                    @if($usuario->cuenta->id_rol == 1 && Auth::user()->id_usuario != 1)
                                        <span class="badge bg-danger">Admin (Protegido)</span>

                                    @elseif(Auth::user()->puedeCambiarRoles())
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

                            {{-- ========================= --}}
                            {{--   COLUMNA: PERMISOS       --}}
                            {{-- ========================= --}}
                            <td>
                                @if($usuario->cuenta && Auth::user()->puedeCambiarRoles())

                                    {{-- Admins protegidos --}}
                                    @if($usuario->cuenta->id_rol == 1 && Auth::user()->id_usuario != 1)
                                        <span class="badge bg-danger">Protegido</span>
                                    @else
                                        <button type="button" class="btn btn-outline-info btn-sm btn-permisos">
                                            <i class="fas fa-cog me-1"></i>Permisos
                                        </button>
                                    @endif

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

                            {{-- ========================= --}}
                            {{--   COLUMNA: ESTADO         --}}
                            {{-- ========================= --}}
                            <td>
                                @if($usuario->cuenta)

                                    {{--Admin protegido --}}
                                    @if($usuario->cuenta->id_rol == 1 && Auth::user()->id_usuario != 1)
                                        <span class="badge bg-danger">No editable</span>

                                    @elseif(Auth::user()->puedeActivarCuentas())
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

                            {{-- ========================= --}}
                            {{--   COLUMNA: ACCIONES       --}}
                            {{-- ========================= --}}
                            <td>
                                <div class="btn-group" role="group">
                                    @if($usuario->cuenta)

                                        {{-- EDITAR --}}
                                        @if(Auth::user()->puedeEditarUsuarios())
                                            @if($usuario->cuenta->id_rol == 1 && Auth::user()->id_usuario != 1)
                                                <span class="badge bg-danger">Protegido</span>
                                            @else
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-edit">
                                                    <i class="fas fa-edit me-1"></i>Editar
                                                </button>
                                            @endif
                                        @endif

                                        {{-- ELIMINAR --}}
                                        @if(Auth::user()->puedeEliminarUsuarios() && $usuario->id_usuario != Auth::user()->id_usuario)
                                            @if($usuario->cuenta->id_rol == 1 && Auth::user()->id_usuario != 1)
                                                <span class="badge bg-danger">No permitido</span>
                                            @else
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-delete">
                                                    <i class="fas fa-trash me-1"></i>Eliminar
                                                </button>
                                            @endif
                                        @endif

                                    @else
                                        {{-- CREAR CUENTA --}}
                                        @if(Auth::user()->puedeCrearUsuarios())
                                            <form action="{{ route('admin.users.create-account', $usuario->id_usuario) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-user-plus me-1"></i>Crear Cuenta
                                                </button>
                                            </form>
                                        @endif

                                        {{-- ELIMINAR --}}
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
                                        <option>Soporte</option>
                                        <option>Redes</option>
                                        <option>Telefonía</option>
                                
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

@section('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {

    const baseUrl = '{{ url("admin/users") }}';

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {

            const tr = this.closest('tr');
            const esAdmin = tr.dataset.rol == "1";
            const esSuper = tr.dataset.esSuper == "1";
            const yoSuper = "{{ Auth::user()->id_usuario }}" == "1";

            if (esAdmin && !yoSuper) {
                alert("Solo el Super Admin puede editar a administradores.");
                return;
            }

            const id = tr.dataset.id;
            const nombre = tr.dataset.nombre;
            const departamento = tr.dataset.departamento;
            const puesto = tr.dataset.puesto;
            const email = tr.dataset.email;
            const username = tr.dataset.username;

const html = `
    <form action="${baseUrl}/${id}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
            <h5 class="modal-title">Editar Usuario: ${nombre}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="${nombre}" required>
            </div>

            <div class="mb-3">
                <label>Departamento</label>
                <input type="text" name="departamento" class="form-control" value="${departamento}">
            </div>

            <div class="mb-3">
                <label>Puesto</label>
                <input type="text" name="puesto" class="form-control" value="${puesto}">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="${email}">
            </div>
<div class="mb-3">
    <label>Nueva Contraseña</label>
    <input type="text" id="password" name="password" class="form-control" placeholder="Dejar vacío para no cambiar">
</div>

<div class="mb-3">
    <label>Confirmar Nueva Contraseña</label>
    <input type="text" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirmar">
</div>


            
            <div class="mb-3">
                <label>Usuario</label>
                <input type="text" name="username" class="form-control" value="${username}">
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
`;


            document.getElementById('editModalContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('globalEditModal')).show();
        });
    });

    document.querySelectorAll('.btn-permisos').forEach(btn => {
        btn.addEventListener('click', function() {

            const tr = this.closest('tr');
            const esAdmin = tr.dataset.rol == "1";
            const yoSuper = "{{ Auth::user()->id_usuario }}" == "1";

            if (esAdmin && !yoSuper) {
                alert("Solo el Super Admin puede editar permisos de administradores.");
                return;
            }

            const id = tr.dataset.id;
            const nombre = tr.dataset.nombre;
            let permisos = JSON.parse(tr.dataset.permisos || "[]");

            const checked = v => permisos.includes(v) ? "checked" : "";

            const html = `
                <form action="${baseUrl}/${id}/update-permissions" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Permisos: ${nombre}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div><input type="checkbox" name="permisos[]" value="1" ${checked(1)}> Ver Usuarios</div>
                        <div><input type="checkbox" name="permisos[]" value="2" ${checked(2)}> Gestionar Formatos</div>
                        <div><input type="checkbox" name="permisos[]" value="3" ${checked(3)}> Crear Usuarios</div>
                        <div><input type="checkbox" name="permisos[]" value="4" ${checked(4)}> Editar Usuarios</div>
                        <div><input type="checkbox" name="permisos[]" value="5" ${checked(5)}> Eliminar Usuarios</div>
                        <div><input type="checkbox" name="permisos[]" value="6" ${checked(6)}> Cambiar Roles</div>
                        <div><input type="checkbox" name="permisos[]" value="7" ${checked(7)}> Activar Cuentas</div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            `;

            document.getElementById('permisosModalContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('globalPermisosModal')).show();
        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {

            const tr = this.closest('tr');
            const esAdmin = tr.dataset.rol == "1";
            const esSuper = tr.dataset.esSuper == "1";
            const yoSuper = "{{ Auth::user()->id_usuario }}" == "1";

            if (esAdmin && !yoSuper) {
                alert("Solo el Super Admin puede eliminar administradores.");
                return;
            }

            if (esSuper) {
                alert("El Super Admin no puede ser eliminado.");
                return;
            }

            const id = tr.dataset.id;
            const nombre = tr.dataset.nombre;

            const html = `
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>¿Deseas eliminar a <strong>${nombre}</strong>?</p>
                    <p class="text-danger fw-bold">Esta acción no se puede deshacer.</p>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="${baseUrl}/${id}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            `;

            document.getElementById('deleteModalContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('globalDeleteModal')).show();
        });

function togglePassword() {
    let f = document.getElementById("password");
    f.type = (f.type === "password") ? "text" : "password";
}
    });



});
</script>
@endsection
