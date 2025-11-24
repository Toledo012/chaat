@extends('layouts.admin')

{{-- ======= Configuración ======= --}}
@section('title', 'Formato A - Soporte / Desarrollo')
@section('header_title', 'Formato A - Soporte / Desarrollo')
@section('header_subtitle', 'Registro y documentación de actividades de soporte')

{{-- ======= Estilos específicos ======= --}}
@section('styles')
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05);
        border: none;
    }
    .card-header {
        background-color: #399e91;
        color: white;
        font-weight: 600;
    }
    .form-control, .form-select {
        border-radius: 8px;
    }
    .btn-primary {
        background-color: #399e91;
        border-color: #399e91;
    }
    .btn-primary:hover {
        background-color: #2f847a;
        border-color: #2f847a;
    }
    .alert-info {
        background-color: #d1f0eb;
        border-color: #399e91;
        color: #25685d;
        font-weight: 500;
    }
</style>
@endsection

{{-- ======= Contenido principal ======= --}}
@section('content')
<div class="alert alert-info mb-4 d-flex align-items-center">
    <i class="fas fa-exclamation-circle me-2"></i>
    Por favor llena todos los campos obligatorios antes de guardar el formato.
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-laptop-code me-2"></i>Formulario de Registro
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.formatos.a.store') }}">
            @csrf

            {{-- NUEVO CAMPO SUBTIPO --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Subtipo <span class="text-danger">*</span></label>
                    
<select name="subtipo" class="form-select" required>
    <option value="">Selecciona...</option>
    <option value="Desarrollo">Desarrollo</option>
    <option value="Soporte">Soporte</option>
</select>

                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo de Atención <span class="text-danger">*</span></label>
                    
                    
<select name="tipo_atencion" class="form-select" required>
    <option value="">Selecciona...</option>
    <option value="Memo">Memo</option>
    <option value="Teléfono">Teléfono</option>
    <option value="Jefe">Jefe</option>
    <option value="Usuario">Usuario</option>
</select>

                </div>
                <div class="col-md-4">
                    <label class="form-label">Petición <span class="text-danger">*</span></label>
                    <input type="text" name="peticion" class="form-control" placeholder="Describe brevemente la solicitud" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tipo de Servicio <span class="text-danger">*</span></label>
                    
                    
<select name="tipo_servicio" class="form-select" required>
    <option value="">Selecciona...</option>
    <option value="Equipos">Equipos</option>
    <option value="Redes LAN/WAN">Redes LAN/WAN</option>
    <option value="Antivirus">Antivirus</option>
    <option value="Software">Software</option>
</select>

                </div>
                <div class="col-md-6">
                    <label class="form-label">Trabajo Realizado <span class="text-danger">*</span></label>
                    
                    
<select name="trabajo_realizado" class="form-select" required>
    <option value="">Selecciona...</option>
    <option value="En sitio">En sitio</option>
    <option value="Área de producción">Área de producción</option>
    <option value="Traslado de equipo">Traslado de equipo</option>
</select>

                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Conclusión del Servicio <span class="text-danger">*</span></label>
                
                
<select name="conclusion_servicio" class="form-select" required>
    <option value="">Selecciona...</option>
    <option value="Terminado">Terminado</option>
    <option value="En proceso">En proceso</option>
</select>

            </div>

            <div class="mb-3">
                <label class="form-label">Trabajo Específico Realizado <span class="text-danger">*</span></label>
                <textarea name="detalle_realizado" class="form-control" rows="3" required></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <input name="firma_usuario" placeholder="Solicitante" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <input name="firma_tecnico" readonly value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <input name="firma_jefe_area" readonly value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2"></textarea>
            </div>

            <div class="text-end">
                <button class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
                <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- ======= Scripts ======= --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('navigation');
    if (sidebar && !sidebar.classList.contains('collapsed')) {
        sidebar.classList.add('collapsed');
        localStorage.setItem('sidebarCollapsed', true);
    }
});
</script>
@endsection
