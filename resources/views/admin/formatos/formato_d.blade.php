@extends('layouts.admin')

@section('title', 'Formato D - Mantenimiento Personal')
@section('header_title', 'Formato D - Entrega y Recepción')
@section('header_subtitle', 'Registro de asignación y resguardo de equipo institucional')

@section('styles')
<style>
    .card-form { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .form-section-title { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #399e91; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px; margin-bottom: 20px; letter-spacing: 0.5px; }
    .form-label, label { font-size: 0.8rem; font-weight: 700; color: #495057; text-transform: uppercase; margin-bottom: 5px; }
    .form-control, .form-select { border-radius: 10px; border: 1px solid #dee2e6; padding: 10px 15px; font-size: 0.9rem; transition: all 0.2s; }
    .form-control:focus { border-color: #399e91; box-shadow: 0 0 0 0.25rem rgba(57, 158, 145, 0.1); }
    .badge-info-custom { background-color: #e0f2f1; color: #00796b; border-radius: 8px; padding: 12px 15px; font-size: 0.85rem; font-weight: 600; }
    .input-group-text { background-color: #f8f9fa; border-radius: 10px; border-right: none; }
</style>
@endsection

@section('content')
<div class="container-fluid px-2">

    {{-- AVISO INFORMATIVO --}}
    <div class="badge-info-custom mb-4 d-flex align-items-center shadow-sm">
        <i class="fas fa-boxes me-3 fa-lg"></i>
        <span>Documentación de Resguardo: Verifique que el número de serie coincida físicamente con el equipo antes de guardar.</span>
    </div>

    <div class="card card-form shadow-sm">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('admin.formatos.d.store') }}">
                @csrf

                {{-- CAMPOS HIDDEN PARA TRAZABILIDAD --}}
                @if(!empty($id_servicio)) <input type="hidden" name="id_servicio" value="{{ $id_servicio }}"> @endif
                @if(!empty($id_ticket)) <input type="hidden" name="id_ticket" value="{{ $id_ticket }}"> @endif

                {{-- SECCIÓN 1: ESPECIFICACIONES DEL EQUIPO --}}
                <div class="form-section-title"><i class="fas fa-laptop me-2"></i>Información del Activo</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Tipo de Equipo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-desktop"></i></span>
                            <input name="equipo" id="equipo" class="form-control shadow-sm" list="equipoList" placeholder="Ej. Laptop, CPU" required>
                        </div>
                        <datalist id="equipoList"></datalist>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Marca <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-copyright"></i></span>
                            <input name="marca" id="marca" class="form-control shadow-sm" list="marcaList" placeholder="Ej. Lenovo, HP" required>
                        </div>
                        <datalist id="marcaList"></datalist>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Modelo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <input name="modelo" id="modelo" class="form-control shadow-sm" list="modeloList" placeholder="Ej. ThinkPad X1" required>
                        </div>
                        <datalist id="modeloList"></datalist>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Número de Serie</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                            <input name="serie" class="form-control shadow-sm fw-bold" placeholder="S/N del fabricante">
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN 2: OBSERVACIONES TÉCNICAS --}}
                <div class="form-section-title"><i class="fas fa-comment-dots me-2"></i>Estado y Observaciones</div>
                <div class="mb-4">
                    <label class="form-label text-muted">Notas de entrega o recepción</label>
                    <textarea name="observaciones" class="form-control shadow-sm" rows="4" placeholder="Describa el estado físico, accesorios entregados o detalles relevantes..."></textarea>
                </div>

                {{-- SECCIÓN 3: FIRMAS Y RESPONSABILIDAD --}}
                <div class="form-section-title"><i class="fas fa-signature me-2"></i>Validación y Firmas</div>
                <div class="row g-3 mb-4 text-start">
                    <div class="col-md-4">
                        <label class="form-label">Persona Otorgante <span class="text-danger">*</span></label>
                        <input name="otorgante" class="form-control shadow-sm" placeholder="Nombre completo" required>
                        <small class="text-muted" style="font-size: 0.65rem;">Quien entrega el equipo</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Persona Receptora (Técnico)</label>
                        <input name="receptor" readonly class="form-control bg-light shadow-sm" value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}">
                        <small class="text-muted" style="font-size: 0.65rem;">Usuario actual del sistema</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Visto Bueno (Jefe de Área)</label>
                        <input name="firma_jefe_area" readonly class="form-control bg-light shadow-sm" value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}">
                        <small class="text-muted" style="font-size: 0.65rem;">Autoridad del departamento</small>
                    </div>
                </div>

                {{-- BOTONES DE ACCIÓN --}}
                <div class="d-flex justify-content-end gap-2 border-top pt-4">
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary px-4 rounded-pill fw-bold">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-save me-1"></i> Guardar Formato D
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Maximizar espacio de trabajo
    const sidebar = document.getElementById('navigation');
    if (sidebar && !sidebar.classList.contains('collapsed')) {
        sidebar.classList.add('collapsed');
        localStorage.setItem('sidebarCollapsed', true);
    }

    // 2. Lógica de Datalists dinámicos con LocalStorage
    function updateDatalist(id, key) {
        const datalist = document.getElementById(id + 'List');
        if(!datalist) return;
        datalist.innerHTML = '';
        const items = JSON.parse(localStorage.getItem(key) || '[]');
        items.forEach(v => {
            const opt = document.createElement('option');
            opt.value = v;
            datalist.appendChild(opt);
        });
    }

    ['equipo', 'marca', 'modelo'].forEach(field => {
        const key = field + 's';
        updateDatalist(field, key);

        const input = document.getElementById(field);
        input.addEventListener('blur', () => {
            const val = input.value.trim();
            if (!val) return;
            
            let arr = JSON.parse(localStorage.getItem(key) || '[]');
            if (!arr.includes(val)) {
                arr.push(val);
                localStorage.setItem(key, JSON.stringify(arr));
                updateDatalist(field, key);
            }
        });
    });
});
</script>
@endsection