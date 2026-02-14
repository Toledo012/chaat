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

{{-- ENCABEZADO --}}
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

{{-- TABLA DE USUARIOS --}}
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
                        data-estado="{{ optional($usuario->cuenta)->estado }}"
                        data-permisos='@json($usuario->cuenta ? ($usuario->cuenta->permisosArray() ?? []) : [])'
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
                                @php $r = match($usuario->cuenta->id_rol) { 1 => ['Admin', 'bg-danger'], 3 => ['Depto', 'bg-dark text-white'], default => ['Usuario', 'bg-secondary text-white'] }; @endphp
                                <span class="badge {{ $r[1] }} rounded-pill mt-1" style="font-size: 0.6rem;">{{ $r[0] }}</span>
                            @else <span class="badge bg-light text-muted border small fw-normal">Sin cuenta activa</span> @endif
                        </td>
                        <td class="text-center">
                            @if($usuario->cuenta) <span class="badge {{ $usuario->cuenta->estado == 'activo' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3" style="font-size: 0.65rem;">{{ strtoupper($usuario->cuenta->estado) }}</span> @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold btn-trigger-gestion" data-user-id="{{ $usuario->id_usuario }}">
                                Gestionar <i class="fas fa-cog ms-1"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ======================================================
     MODALES GLOBALES (FUERA DEL LOOP)
     ====================================================== --}}

{{-- 1. MODAL GESTIÓN RÁPIDA --}}
<div class="modal fade" id="modalGestionGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h6 class="modal-title fw-bold" id="gestionTitle">Administrar Perfil</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div id="gestionAvatar" class="user-avatar mx-auto mb-3 shadow-sm" style="width: 55px; height: 55px; font-size: 1.3rem;">U</div>
                <h5 id="gestionNombre" class="fw-bold mb-4 border-bottom pb-2">Nombre Usuario</h5>

                <div class="d-grid gap-2 text-start">
                    <div id="rolUpdateSection" class="mb-3 d-none">
                        <label class="modal-label-header">Actualizar Rol</label>
                        <form id="formUpdateRol" method="POST">
                            @csrf @method('PUT')
                            <div class="input-group input-group-sm">
                                <select name="rol" id="selectRol" class="form-select">
                                    <option value="1">Administrador</option>
                                    <option value="2">Usuario</option>
                                    <option value="3">Departamento</option>
                                </select>
                                <button class="btn btn-primary px-3 shadow-sm">Cambiar</button>
                            </div>
                        </form>
                    </div>

                    <div class="row g-2" id="botonesAccionSection">
                        {{-- Se llena con JS --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. MODAL EDITAR PERFIL --}}
<div class="modal fade" id="globalEditModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" id="editModalContent">
            {{-- Llenado dinámico --}}
        </div>
    </div>
</div>

{{-- 3. MODAL PERMISOS --}}
<div class="modal fade" id="globalPermisosModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" id="permisosModalContent">
            {{-- Llenado dinámico --}}
        </div>
    </div>
</div>

{{-- 4. MODAL ELIMINAR --}}
<div class="modal fade" id="globalDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" id="deleteModalContent">
            {{-- Llenado dinámico --}}
        </div>
    </div>
</div>

{{-- 5. MODAL CREAR USUARIO (ESTÁTICO - SIEMPRE FUNCIONA) --}}
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered shadow">
        <div class="modal-content border-0">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold">Registrar Personal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="modal-label-header">Nombre Completo *</label>
                        <input type="text" class="form-control shadow-sm" name="nombre" required placeholder="Nombre y Apellidos">
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="modal-label-header">Departamento</label>
                            <select name="id_departamento" class="form-select shadow-sm" required>
                                <option value="">Seleccionar...</option>
                                @foreach($departamentos as $dep)
                                    <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="modal-label-header">Puesto / Cargo</label>
                            <select name="puesto" class="form-select shadow-sm">
                                <option value="">Seleccionar...</option>
                                <option>Jefe de Area</option>
                                <option>Soporte</option>
                                <option>Programador</option>
                                <option>Enlace</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="modal-label-header">Login (Username) *</label>
                            <input type="text" class="form-control shadow-sm" name="username" required placeholder="ej. juan.perez">
                        </div>
                        <div class="col-6">
                            <label class="modal-label-header">Contraseña *</label>
                            <div class="input-group input-group-sm">
                                <input type="password" class="form-control shadow-sm" name="password" id="passInputNew" required>
                                <span class="input-group-text bg-white" onclick="togglePass('passInputNew')"><i class="fas fa-eye"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="modal-label-header">Extensión</label>
                            <input type="text" class="form-control shadow-sm" name="extension" placeholder="Ej. 102">
                        </div>
                        <div class="col-6">
                            <label class="modal-label-header">Email Institucional *</label>
                            <input type="email" class="form-control shadow-sm" name="email" required placeholder="correo@ejemplo.com">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="modal-label-header text-primary">Rol Inicial</label>
                        <select name="rol" class="form-select border-primary shadow-sm" required>
                            <option value="2">Usuario (Soporte)</option>
                            <option value="1">Administrador</option>
                            <option value="3">Departamento (Solicitante)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 rounded-pill shadow-sm">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- TEMPLATE OCULTO PARA SELECT DE DEPARTAMENTOS --}}
<div id="departamentoSelectTemplate" class="d-none">
    @foreach($departamentos as $dep)
        <option value="{{ $dep->id_departamento }}">{{ $dep->nombre }}</option>
    @endforeach
</div>

@endsection

{{-- ======= SCRIPTS ======= --}}
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
    const authUserId = "{{ Auth::user()->id_usuario }}";
    const canChangeRoles = {{ Auth::user()->puedeCambiarRoles() ? 'true' : 'false' }};

    // --- ACTIVADOR MODAL GESTIÓN ---
    document.querySelectorAll('.btn-trigger-gestion').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const tr = document.querySelector(`tr[data-id="${userId}"]`);
            if(!tr) return;

            // Llenar Modal Gestión
            document.getElementById('gestionNombre').innerText = tr.dataset.nombre;
            document.getElementById('gestionAvatar').innerText = tr.dataset.nombre.charAt(0).toUpperCase();
            
            // Sección Rol
            const rolSection = document.getElementById('rolUpdateSection');
            if(tr.dataset.rol && canChangeRoles) {
                rolSection.classList.remove('d-none');
                document.getElementById('formUpdateRol').action = `${baseUrl}/${userId}/update-role`;
                document.getElementById('selectRol').value = tr.dataset.rol;
            } else {
                rolSection.classList.add('d-none');
            }

            // Botones de Acción
            let botonesHtml = '';
            if(tr.dataset.username) { // Si tiene cuenta
                botonesHtml = `
                    <div class="col-12">
                        <form action="${baseUrl}/${userId}/reset-password" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-warning w-100 fw-bold shadow-sm mb-2" onclick="return confirm('¿Restablecer contraseña?')">
                                <i class="fas fa-key me-2"></i> Resetear Contraseña
                            </button>
                        </form>
                    </div>
                    <div class="col-12">
                        <form action="${baseUrl}/${userId}/toggle-status" method="POST">
                            @csrf @method('PUT')
                            <button class="btn btn-sm w-100 fw-bold border mb-2 ${tr.dataset.estado == 'activo' ? 'btn-outline-dark' : 'btn-success shadow-sm'}">
                                <i class="fas ${tr.dataset.estado == 'activo' ? 'fa-user-slash' : 'fa-user-check'} me-2"></i>
                                ${tr.dataset.estado == 'activo' ? 'Suspender Acceso' : 'Activar Acceso'}
                            </button>
                        </form>
                    </div>
                    <div class="col-6"><button class="btn btn-outline-primary btn-sm w-100 fw-bold py-2 shadow-sm" onclick="abrirEditar(${userId})">Editar Perfil</button></div>
                    <div class="col-6"><button class="btn btn-outline-info btn-sm w-100 fw-bold py-2 shadow-sm" onclick="abrirPermisos(${userId})">Permisos</button></div>
                `;
            } else {
                botonesHtml = `
                    <div class="col-12">
                        <form action="${baseUrl}/${userId}/create-account" method="POST">
                            @csrf
                            <button class="btn btn-success btn-sm w-100 fw-bold py-2 mb-2 shadow-sm text-white">Habilitar Acceso Sistema</button>
                        </form>
                    </div>
                `;
            }
            botonesHtml += `<div class="col-12 mt-2 pt-2 border-top text-center"><button class="btn btn-link text-danger btn-sm text-decoration-none fw-bold" onclick="abrirEliminar(${userId})">Eliminar Definitivamente</button></div>`;
            
            document.getElementById('botonesAccionSection').innerHTML = botonesHtml;
            new bootstrap.Modal(document.getElementById('modalGestionGlobal')).show();
        });
    });

    // --- FUNCIONES DE APERTURA (Usadas por los botones inyectados) ---

    window.abrirEditar = function(userId) {
        const tr = document.querySelector(`tr[data-id="${userId}"]`);
        bootstrap.Modal.getInstance(document.getElementById('modalGestionGlobal')).hide();

        const options = document.getElementById('departamentoSelectTemplate').innerHTML;
        const html = `
            <form action="${baseUrl}/${userId}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0"><h6 class="modal-title fw-bold">Actualizar Datos</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-4">
                    <div class="mb-3"><label class="modal-label-header">Nombre Completo</label><input type="text" name="nombre" class="form-control shadow-sm" value="${tr.dataset.nombre}" required></div>
                    <div class="mb-3"><label class="modal-label-header">Departamento</label><select name="id_departamento" id="editDepto" class="form-select shadow-sm" required>${options}</select></div>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><label class="modal-label-header">Puesto</label><input type="text" name="puesto" class="form-control shadow-sm" value="${tr.dataset.puesto || ''}" required></div>
                        <div class="col-6"><label class="modal-label-header">Extensión</label><input type="text" name="extension" class="form-control shadow-sm" value="${tr.dataset.extension || ''}"></div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><label class="modal-label-header">Email</label><input type="email" name="email" class="form-control shadow-sm" value="${tr.dataset.email}" required></div>
                        <div class="col-6"><label class="modal-label-header text-primary">Username</label><input type="text" name="username" class="form-control border-primary shadow-sm" value="${tr.dataset.username || ''}" required></div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light"><button type="submit" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm">Guardar Cambios</button></div>
            </form>
        `;
        document.getElementById('editModalContent').innerHTML = html;
        document.getElementById('editDepto').value = tr.dataset.departamentoId;
        new bootstrap.Modal(document.getElementById('globalEditModal')).show();
    };

    window.abrirPermisos = function(userId) {
        const tr = document.querySelector(`tr[data-id="${userId}"]`);
        if (tr.dataset.rol == "1" && authUserId != "1") { alert("Acceso denegado."); return; }
        bootstrap.Modal.getInstance(document.getElementById('modalGestionGlobal')).hide();

        const p = JSON.parse(tr.dataset.permisos || "[]");
        const ck = v => p.includes(v) ? "checked" : "";
        
        const html = `
            <form action="${baseUrl}/${userId}/update-permissions" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0"><h6 class="modal-title fw-bold">Privilegios: ${tr.dataset.nombre}</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="permission-group-title"><i class="fas fa-building me-1"></i> Depto</div>
                            <div class="form-check small"><input type="checkbox" name="permisos[]" value="12" ${ck(12)} class="form-check-input"> <label>tickets.crear</label></div>
                            <div class="form-check small"><input type="checkbox" name="permisos[]" value="13" ${ck(13)} class="form-check-input"> <label>tickets.ver_propios</label></div>
                            <div class="form-check small"><input type="checkbox" name="permisos[]" value="14" ${ck(14)} class="form-check-input"> <label>tickets.editar_propios</label></div>
                        </div>
                        <div class="col-md-4 border-start border-end">
                            <div class="permission-group-title"><i class="fas fa-user-tag me-1"></i> Técnico</div>
                            <div class="form-check small"><input type="checkbox" name="permisos[]" value="16" ${ck(16)} class="form-check-input"> <label>tickets.tomar</label></div>
                            <div class="form-check small"><input type="checkbox" name="permisos[]" value="17" ${ck(17)} class="form-check-input"> <label>tickets.ver_asignados</label></div>
                            <div class="form-check small mt-2 fw-bold"><input type="checkbox" name="permisos[]" value="2" ${ck(2)} class="form-check-input"> <label>gestion_formatos</label></div>
                        </div>
                        <div class="col-md-4">
                            <div class="permission-group-title"><i class="fas fa-crown me-1"></i> Admin</div>
                            <div class="form-check small"><input type="checkbox" name="permisos[]" value="20" ${ck(20)} class="form-check-input"> <label>tickets.ver_todos</label></div>
                            <div class="form-check small"><input type="checkbox" name="permisos[]" value="1" ${ck(1)} class="form-check-input"> <label>gestion_usuarios</label></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light"><button type="submit" class="btn btn-info btn-sm fw-bold px-3 text-white shadow-sm">Actualizar</button></div>
            </form>
        `;
        document.getElementById('permisosModalContent').innerHTML = html;
        new bootstrap.Modal(document.getElementById('globalPermisosModal')).show();
    };

    window.abrirEliminar = function(userId) {
        const tr = document.querySelector(`tr[data-id="${userId}"]`);
        bootstrap.Modal.getInstance(document.getElementById('modalGestionGlobal')).hide();
        
        document.getElementById('deleteModalContent').innerHTML = `
            <div class="modal-header bg-danger text-white border-0"><h6 class="modal-title fw-bold">Eliminar</h6><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                <p>¿Deseas eliminar a <b>${tr.dataset.nombre}</b> permanentemente?</p>
            </div>
            <div class="modal-footer border-0 bg-light">
                <form action="${baseUrl}/${userId}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger btn-sm px-4 shadow-sm">Eliminar Ahora</button></form>
            </div>
        `;
        new bootstrap.Modal(document.getElementById('globalDeleteModal')).show();
    };
});
</script>
@endsection