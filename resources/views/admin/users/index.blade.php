@extends('layouts.admin')

{{-- ======= CONFIGURACIÓN DE TÍTULOS ======= --}}
@section('title', 'Gestión de Usuarios - Sistema de Formatos')
@section('header_title', 'Gestión de Usuarios')
@section('header_subtitle', 'Control de roles, permisos y estado de las cuentas')

{{-- ======= ESTILOS ESPECÍFICOS ======= --}}
@section('styles')
<style>
    .user-avatar { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: #f0f7f6; color: #399e91; font-weight: bold; border: 1px solid #d1e7e4; }
    .modal-label-header { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #495057; margin-bottom: 0.2rem; display: block; }
    .permission-group-title { font-size: 0.85rem; font-weight: bold; color: #399e91; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; }
    .input-group-text { cursor: pointer; }
</style>
@endsection

{{-- ======= CONTENIDO PRINCIPAL ======= --}}
@section('content')

{{-- ALERTAS --}}
<div class="container-fluid mb-2">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        @if(session('error')) {{ session('error') }} @else Revisa los campos resaltados. @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
</div>

{{-- ENCABEZADO ESTILO TICKETS --}}
<div class="d-flex align-items-center gap-3 mb-4 px-2">
    <i class="fas fa-users-cog text-primary fa-2x"></i>
    <div>
        <h4 class="mb-0 fw-bold">Usuarios</h4>
        <p class="text-muted mb-0 small text-uppercase">Bandeja principal de gestión de personal</p>
    </div>
    @if(Auth::user()->puedeCrearUsuarios())
        <button class="btn btn-primary ms-auto shadow-sm fw-bold btn-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-user-plus me-2"></i> Crear Usuario
        </button>
    @endif
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase text-muted">
                    <tr>
                        <th class="ps-4">Perfil / Datos</th>
                        <th>Departamento / Cargo</th>
                        <th>Acceso Sistema</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr 
                        data-id="{{ $usuario->id_usuario }}" 
                        data-rol="{{ optional($usuario->cuenta)->id_rol }}" 
                        data-nombre="{{ $usuario->nombre }}" 
                        data-departamento-id="{{ $usuario->id_departamento }}" 
                        data-puesto="{{ $usuario->puesto }}" 
                        data-extension="{{ $usuario->extension }}"
                        data-email="{{ $usuario->email }}" 
                        data-username="{{ optional($usuario->cuenta)->username }}" 
                        data-permisos='@json($usuario->cuenta->permisosArray() ?? [])'
                    >
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar shadow-sm border">{{ strtoupper(substr($usuario->nombre, 0, 1)) }}</div>
                                <div>
                                    <div class="fw-bold text-dark small">{{ $usuario->nombre }} @if($usuario->id_usuario == 1) <span class="badge bg-danger ms-1" style="font-size:0.5rem">SUPER</span> @endif</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">{{ $usuario->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $usuario->departamentos->nombre ?? 'N/A' }}</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $usuario->puesto }} @if($usuario->extension) <span class="ms-1 border-start ps-1 text-primary fw-bold">Ext. {{ $usuario->extension }}</span> @endif</div>
                        </td>
                        <td>
                            @if($usuario->cuenta)
                                <span class="badge bg-white text-primary border border-primary-subtle px-2">{{ $usuario->cuenta->username }}</span><br>
                                @php $r = match($usuario->cuenta->id_rol) { 1 => ['Admin', 'bg-danger'], 4 => ['Depto', 'bg-dark text-white'], default => ['Usuario', 'bg-secondary text-white'] }; @endphp
                                <span class="badge {{ $r[1] }} rounded-pill mt-1" style="font-size: 0.6rem;">{{ $r[0] }}</span>
                            @else <span class="badge bg-light text-muted border small fw-normal">Sin cuenta activa</span> @endif
                        </td>
                        <td class="text-center">
                            @if($usuario->cuenta) <span class="badge {{ $usuario->cuenta->estado == 'activo' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3" style="font-size: 0.65rem;">{{ strtoupper($usuario->cuenta->estado) }}</span> @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGestionUsuario{{ $usuario->id_usuario }}">Gestionar <i class="fas fa-chevron-right ms-1"></i></button>
                        </td>
                    </tr>

                    {{-- MODAL GESTIÓN --}}
                    <div class="modal fade" id="modalGestionUsuario{{ $usuario->id_usuario }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-light border-bottom">
                                    <h6 class="modal-title fw-bold">Administrar Perfil: {{ $usuario->nombre }}</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 text-center">
                                    <div class="user-avatar mx-auto mb-3 shadow-sm" style="width: 55px; height: 55px; font-size: 1.3rem;">{{ strtoupper(substr($usuario->nombre, 0, 1)) }}</div>
                                    <h5 class="fw-bold mb-4 border-bottom pb-2">{{ $usuario->nombre }}</h5>

                                    <div class="d-grid gap-2 text-start">
                                        @if($usuario->cuenta && Auth::user()->puedeCambiarRoles())
                                            <div class="mb-3">
                                                <label class="modal-label-header">Actualizar Rol</label>
                                                <form action="{{ route('admin.users.update-role', $usuario->id_usuario) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="input-group input-group-sm">
                                                        <select name="rol" class="form-select">
                                                            <option value="1" @selected($usuario->cuenta->id_rol == 1)>Administrador</option>
                                                            <option value="2" @selected($usuario->cuenta->id_rol == 2)>Usuario</option>
                                                            <option value="4" @selected($usuario->cuenta->id_rol == 4)>Departamento</option>
                                                        </select>
                                                        <button class="btn btn-primary px-3 shadow-sm">Cambiar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif

                                        <div class="row g-2">
                                            @if($usuario->cuenta)
                                                <div class="col-12">
                                                    <form action="{{ route('admin.users.reset-password', $usuario->id_usuario) }}" method="POST">
                                                        @csrf
                                                        <button class="btn btn-sm btn-warning w-100 fw-bold shadow-sm mb-2" onclick="return confirm('¿Restablecer a contraseña predeterminada del sistema?')">
                                                            <i class="fas fa-key me-2"></i> Resetear Contraseña
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="col-12">
                                                    <form action="{{ route('admin.users.toggle-status', $usuario->id_usuario) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <button class="btn btn-sm w-100 fw-bold border mb-2 {{ $usuario->cuenta->estado == 'activo' ? 'btn-outline-dark text-muted' : 'btn-success shadow-sm' }}">
                                                            <i class="fas {{ $usuario->cuenta->estado == 'activo' ? 'fa-user-slash' : 'fa-user-check' }} me-2"></i>
                                                            {{ $usuario->cuenta->estado == 'activo' ? 'Suspender Acceso' : 'Activar Acceso' }}
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="col-6"><button class="btn btn-outline-primary btn-sm w-100 fw-bold py-2 shadow-sm btn-trigger-edit" data-user-id="{{ $usuario->id_usuario }}">Editar Perfil</button></div>
                                                <div class="col-6"><button class="btn btn-outline-info btn-sm w-100 fw-bold py-2 shadow-sm btn-trigger-permisos" data-user-id="{{ $usuario->id_usuario }}">Permisos</button></div>
                                            @else
                                                <div class="col-12"><form action="{{ route('admin.users.create-account', $usuario->id_usuario) }}" method="POST">@csrf<button class="btn btn-success btn-sm w-100 fw-bold py-2 mb-2 shadow-sm">Habilitar Acceso Sistema</button></form></div>
                                            @endif
                                            <div class="col-12 mt-2 pt-2 border-top text-center"><button class="btn btn-link text-danger btn-sm text-decoration-none fw-bold btn-trigger-delete" data-user-id="{{ $usuario->id_usuario }}">Eliminar de la Base de Datos</button></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODALES GLOBALES --}}
<div class="modal fade" id="globalEditModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg" id="editModalContent"></div></div></div>
<div class="modal fade" id="globalPermisosModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content border-0 shadow-lg" id="permisosModalContent"></div></div></div>
<div class="modal fade" id="globalDeleteModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg" id="deleteModalContent"></div></div></div>

{{-- MODAL CREAR USUARIO --}}
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered shadow">
        <div class="modal-content border-0">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0"><h5 class="modal-title fw-bold">Registrar Personal</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-4">
                    <div class="mb-3"><label class="modal-label-header">Nombre Completo *</label><input type="text" class="form-control shadow-sm" name="nombre" required placeholder="Nombre y Apellidos"></div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6"><label class="modal-label-header">Departamento</label><select name="id_departamento" class="form-select shadow-sm" required><option value="">Seleccionar...</option>@foreach($departamentos as $dep)<option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>@endforeach</select></div>
                        <div class="col-6"><label class="modal-label-header">Puesto / Cargo</label><select name="puesto" class="form-select shadow-sm"><option value="">Seleccionar...</option><option>Jefe de Area</option><option>Técnico</option><option>Programador</option></select></div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6"><label class="modal-label-header">Email (Login) *</label><input type="text" class="form-control shadow-sm" name="username" required placeholder="juan.perez"></div>
                        <div class="col-6">
                            <label class="modal-label-header">Contraseña *</label>
                            <div class="input-group input-group-sm">
                                <input type="password" class="form-control shadow-sm" name="password" id="passInput" required>
                                <span class="input-group-text bg-white" onclick="togglePass('passInput')"><i class="fas fa-eye" id="passIcon"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6"><label class="modal-label-header">Extensión Telefónica</label><input type="text" class="form-control shadow-sm" name="extension" placeholder="Ej. 102"></div>
                        <div class="col-6"><label class="modal-label-header">Confirmar Email *</label><input type="email" class="form-control shadow-sm" name="email" required placeholder="correo@ejemplo.com"></div>
                    </div>

                    <div class="mb-0"><label class="modal-label-header text-primary">Asignación de Rol Inicial</label><select name="rol" class="form-select border-primary shadow-sm" required><option value="2">Usuario</option><option value="1">Administrador</option><option value="4">Departamento</option></select></div>
                </div>
                <div class="modal-footer bg-light border-0"><button type="submit" class="btn btn-primary btn-sm fw-bold px-4 rounded-pill shadow-sm">Guardar Registro</button></div>
            </form>
        </div>
    </div>
</div>

<select id="departamentoSelectTemplate" class="d-none">@foreach($departamentos as $dep)<option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>@endforeach</select>

@endsection

@section('scripts')
<script>
// Función ver/ocultar password
function togglePass(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling.querySelector('i');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = '{{ url("admin/users") }}';

    function closeManagerModal(userId) {
        const modalEl = document.getElementById('modalGestionUsuario' + userId);
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
    }

    // EDITAR PERFIL (Con extensión)
    document.querySelectorAll('.btn-trigger-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const tr = document.querySelector(`tr[data-id="${userId}"]`);
            closeManagerModal(userId);
            const selectTemplate = document.getElementById('departamentoSelectTemplate').innerHTML;
            const html = `
                <form action="${baseUrl}/${tr.dataset.id}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header border-0 pb-0"><h6 class="modal-title fw-bold">Actualizar Datos de Perfil</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body p-4">
                        <div class="mb-3"><label class="modal-label-header">Nombre Completo</label><input type="text" name="nombre" class="form-control shadow-sm" value="${tr.dataset.nombre}" required></div>
                        <div class="mb-3"><label class="modal-label-header">Adscripción / Departamento</label><select name="id_departamento" class="form-select shadow-sm" required>${selectTemplate}</select></div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="modal-label-header">Cargo / Puesto</label><input type="text" name="puesto" class="form-control shadow-sm" value="${tr.dataset.puesto}"></div>
                            <div class="col-6"><label class="modal-label-header">Extensión</label><input type="text" name="extension" class="form-control shadow-sm" value="${tr.dataset.extension || ''}"></div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><label class="modal-label-header">Email</label><input type="email" name="email" class="form-control shadow-sm" value="${tr.dataset.email}"></div>
                            <div class="col-6"><label class="modal-label-header text-primary">Login de Usuario</label><input type="text" name="username" class="form-control border-primary shadow-sm" value="${tr.dataset.username}"></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light"><button type="submit" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm">Guardar Cambios</button></div>
                </form>
            `;
            document.getElementById('editModalContent').innerHTML = html;
            setTimeout(() => {
                const select = document.querySelector('#globalEditModal select[name="id_departamento"]');
                if (select) select.value = tr.dataset.departamentoId;
                new bootstrap.Modal(document.getElementById('globalEditModal')).show();
            }, 300);
        });
    });

    // PERMISOS (CON LOS 25 IDS ORGANIZADOS)
    document.querySelectorAll('.btn-trigger-permisos').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const tr = document.querySelector(`tr[data-id="${userId}"]`);
            if (tr.dataset.rol == "1" && "{{ Auth::user()->id_usuario }}" != "1") { alert("Solo el Super Admin gestiona a otros administradores."); return; }
            closeManagerModal(userId);
            const p = JSON.parse(tr.dataset.permisos || "[]");
            const ck = v => p.includes(v) ? "checked" : "";
            const html = `
                <form action="${baseUrl}/${tr.dataset.id}/update-permissions" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header border-0 pb-0"><h6 class="modal-title fw-bold">Privilegios: ${tr.dataset.nombre}</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="permission-group-title"><i class="fas fa-building me-1"></i> Departamento</div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="12" ${ck(12)} class="form-check-input"> <label>tickets.crear</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="13" ${ck(13)} class="form-check-input"> <label>tickets.ver_propios</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="14" ${ck(14)} class="form-check-input"> <label>tickets.editar_propios</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="15" ${ck(15)} class="form-check-input"> <label>tickets.cancelar_propios</label></div>
                            </div>
                            <div class="col-md-4 border-start border-end">
                                <div class="permission-group-title"><i class="fas fa-user-tag me-1"></i> Usuario</div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="16" ${ck(16)} class="form-check-input"> <label>tickets.tomar</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="17" ${ck(17)} class="form-check-input"> <label>tickets.ver_asignados</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="18" ${ck(18)} class="form-check-input"> <label>tickets.actualizar_avance</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="19" ${ck(19)} class="form-check-input"> <label>tickets.completar</label></div>
                                <div class="form-check small mt-2 fw-bold"><input type="checkbox" name="permisos[]" value="2" ${ck(2)} class="form-check-input"> <label>gestion_formatos</label></div>
                            </div>
                            <div class="col-md-4">
                                <div class="permission-group-title"><i class="fas fa-crown me-1"></i> Admin</div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="20" ${ck(20)} class="form-check-input"> <label>tickets.ver_todos</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="21" ${ck(21)} class="form-check-input"> <label>tickets.asignar</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="22" ${ck(22)} class="form-check-input"> <label>tickets.reasignar</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="23" ${ck(23)} class="form-check-input"> <label>tickets.cambiar_prio</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="24" ${ck(24)} class="form-check-input"> <label>tickets.cambiar_est</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="25" ${ck(25)} class="form-check-input"> <label>tickets.cancelar</label></div>
                                <div class="form-check small mt-2 pt-2 border-top"><input type="checkbox" name="permisos[]" value="1" ${ck(1)} class="form-check-input"> <label>gestion_usuarios</label></div>
                                <div class="form-check small"><input type="checkbox" name="permisos[]" value="6" ${ck(6)} class="form-check-input"> <label>cambiar_roles</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light"><button type="submit" class="btn btn-info btn-sm fw-bold px-3 shadow-sm">Actualizar Privilegios</button></div>
                </form>
            `;
            document.getElementById('permisosModalContent').innerHTML = html;
            setTimeout(() => { new bootstrap.Modal(document.getElementById('globalPermisosModal')).show(); }, 300);
        });
    });

    // ELIMINAR
    document.querySelectorAll('.btn-trigger-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const tr = document.querySelector(`tr[data-id="${userId}"]`);
            if (tr.dataset.rol == "1" && "{{ Auth::user()->id_usuario }}" != "1") { alert("Acción denegada."); return; }
            closeManagerModal(userId);
            document.getElementById('deleteModalContent').innerHTML = `
                <div class="modal-header bg-danger text-white border-0"><h6 class="modal-title fw-bold">Confirmar Acción Irreversible</h6><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-4 text-center"><i class="fas fa-user-times text-danger fa-3x mb-3 opacity-75"></i><p class="fw-bold mb-1">¿Deseas eliminar a ${tr.dataset.nombre}?</p><p class="text-muted small">Esta operación borrará su cuenta y perfil permanentemente.</p></div>
                <div class="modal-footer border-0 bg-light"><form action="${baseUrl}/${userId}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger btn-sm fw-bold shadow-sm px-4">Eliminar Ahora</button></form></div>
            `;
            setTimeout(() => { new bootstrap.Modal(document.getElementById('globalDeleteModal')).show(); }, 300);
        });
    });
});
</script>
@endsection