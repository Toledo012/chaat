@extends('layouts.admin')

@section('title', 'Formato R - Recepción')

@section('styles')
    <style>
        .card-header { background-color: #399e91; color: white; font-weight: 600; }
        .form-control, .form-select { border-radius: 8px; }
        .btn-primary { background-color: #399e91; border-color: #399e91; }
        .btn-primary:hover { background-color: #2f847a; border-color: #2f847a; }
        .alert-info { background-color: #d1f0eb; border-color: #399e91; color: #25685d; font-weight: 500; }
    </style>
@endsection

@section('content')
    <div class="alert alert-info mb-4 d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-2"></i>
        Llena todos los campos obligatorios antes de guardar el formato.
    </div>

    <div class="card shadow border-0">
        <div class="card-header">
            <i class="fas fa-box-open me-2"></i>Formulario de Formato R
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.formatos.r.store') }}">
                @csrf

                <input type="hidden" name="id_servicio" value="{{ request('id_servicio') }}">
                <input type="hidden" name="id_ticket" value="{{ request('id_ticket') }}">

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase">
                            Departamento <span class="text-danger">*</span>
                        </label>

                        @if($ticketDeptId)
                            <select name="id_departamento" class="form-select" disabled>
                                @foreach($departamentos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE) as $dep)
                                    <option value="{{ $dep->id_departamento }}"
                                        {{ $ticketDeptId == $dep->id_departamento ? 'selected' : '' }}>
                                        {{ $dep->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="id_departamento" value="{{ $ticketDeptId }}">
                        @else
                            <div class="input-group">
                                <select id="selectDepartamentoFormatoR" name="id_departamento" class="form-select" required>
                                    <option value="">Selecciona un departamento</option>
                                    @foreach($departamentos->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE) as $dep)
                                        <option value="{{ $dep->id_departamento }}"
                                            {{ old('id_departamento') == $dep->id_departamento ? 'selected' : '' }}>
                                            {{ $dep->nombre }}
                                        </option>
                                    @endforeach
                                </select>

                                <button type="button" class="btn btn-outline-success fw-bold"
                                        data-bs-toggle="modal" data-bs-target="#modalCrearDepartamentoFormatoR">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">Si no existe, crea uno aquí mismo.</small>
                        @endif

                        @error('id_departamento')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label">
                        Descripción de objetos recibidos <span class="text-danger">*</span>
                    </label>
                    <textarea name="descripcion"
                              class="form-control"
                              rows="6"
                              required
                              placeholder="Describe los objetos recibidos, cantidad, características, estado, observaciones, etc.">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <hr>
                <h6 class="text-primary mb-3">
                    <i class="fas fa-signature me-1"></i> Firmas y Validación
                </h6>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Usuario que entrega <span class="text-danger">*</span></label>
                        <input name="firma_usuario"
                               class="form-control"
                               placeholder="Nombre de quien entrega"
                               value="{{ old('firma_usuario') }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Técnico responsable</label>
                        <input name="firma_tecnico"
                               readonly
                               class="form-control bg-light"
                               value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}">
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar</button>
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    @if(!$ticketDeptId)
        <div class="modal fade" id="modalCrearDepartamentoFormatoR" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-building me-2"></i> Registrar Departamento
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formCrearDepartamentoFormatoR">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nombre *</label>
                                <input type="text" name="nombre" class="form-control shadow-sm" required maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Descripción</label>
                                <input type="text" name="descripcion" class="form-control shadow-sm" maxlength="100">
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="submit" class="btn btn-success fw-bold px-4 rounded-pill">
                                <i class="fas fa-save me-1"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const formDepto = document.getElementById('formCrearDepartamentoFormatoR');
            if (!formDepto) return;

            formDepto.addEventListener('submit', async (e) => {
                e.preventDefault();

                const btn = formDepto.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'Guardando...';

                try {
                    const resp = await fetch('{{ route("admin.departamentos.quickStore") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(Object.fromEntries(new FormData(formDepto))),
                    });

                    const data = await resp.json();

                    if (!resp.ok) {
                        throw new Error(data.message || 'No se pudo crear el departamento.');
                    }

                    const select = document.getElementById('selectDepartamentoFormatoR');
                    const opt = new Option(
                        data.nombre,
                        data.id_departamento,
                        true,
                        true
                    );

                    select.appendChild(opt);
                    select.value = data.id_departamento;

                    bootstrap.Modal.getInstance(
                        document.getElementById('modalCrearDepartamentoFormatoR')
                    ).hide();

                    formDepto.reset();

                } catch (error) {
                    alert(error.message || 'Error de conexión.');
                } finally {
                    btn.disabled = false;
                    btn.textContent = 'Guardar';
                }
            });
        });
    </script>
@endsection
