<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista previa - Formato B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f8f9fa; padding:2rem; }
        .header { border-bottom:3px solid #399e91; padding-bottom:10px; margin-bottom:20px; }
        .header img { width:110px; }
        .titulo   { text-align:center; font-weight:bold; color:#0a3622; text-transform:uppercase; margin-top:.5rem; }
        .subtitulo{ text-align:center; font-size:13px; color:#6c757d; margin-bottom:1rem; }
        .section-title { background:#d1e7dd; color:#0a3622; font-weight:bold;
            padding:6px 10px; border-radius:4px; margin-top:1rem; margin-bottom:.4rem; }
        table { width:100%; border-collapse:collapse; }
        table td, table th { border:1px solid #dee2e6; padding:8px; vertical-align:top; }
        table th { background:#f8f9fa; font-weight:600; }
        .firmas td { border:none; text-align:center; padding-top:30px; }
        .footer { text-align:center; font-size:13px; color:#6c757d;
            border-top:2px solid #dee2e6; margin-top:2rem; padding-top:10px; }
        .edicion { display:none; }
        .badge-memo { display:inline-block; font-size:.75rem; background:#d1f0eb; color:#155d50;
            border:1px solid #399e91; border-radius:6px; padding:1px 8px; margin-left:6px; font-weight:600; }
    </style>
</head>
<body>
<div class="container bg-white shadow p-4 rounded">

    {{-- Barra acciones --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" onclick="toggleEdicion()">
                <i class="fa-solid fa-pen-to-square me-1"></i> Editar Formato
            </button>
            <a href="{{ route('admin.formatos.b.pdf', $servicio->id_servicio) }}"
               target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.formatos.update', ['B', $servicio->id_servicio]) }}">
        @csrf

        {{-- Encabezado institucional --}}
        <div class="row align-items-center header">
            <div class="col-3 text-center">
                <img src="{{ asset('images/logo_semahn2.png') }}" alt="SEMAHN">
            </div>
            <div class="col-9 text-center">
                <strong>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</strong><br>
                UNIDAD DE APOYO ADMINISTRATIVO — ÁREA DE INFORMÁTICA<br>
                <small><em>"2025, Año de Rosario Castellanos Figueroa"</em></small>
            </div>
        </div>

        <h5 class="titulo">Formato B — Equipos de Cómputo / Impresoras</h5>
        <p class="subtitulo">Mantenimiento preventivo o correctivo de equipos</p>

        {{-- Datos Generales --}}
        <div class="section-title">Datos del Servicio</div>
        <table class="table table-bordered">
            <tr>
                <th width="20%">Folio</th>
                <td>{{ $servicio->folio ?? 'ID-'.$servicio->id_servicio }}</td>
                <th width="20%">Fecha</th>
                <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Tipo de Atención</th>
                <td>
                    {{-- VISTA --}}
                    <span class="vista">
                        {{ $servicio->tipo_atencion }}
                        @if($servicio->tipo_atencion === 'Memo' && $servicio->num_memo)
                            <span class="badge-memo">Memo N° {{ $servicio->num_memo }}</span>
                        @endif
                    </span>
                    {{-- EDICIÓN --}}
                    <div class="edicion">
                        <select name="tipo_atencion" id="tipoAtencionEdit" class="form-select">
                            @foreach(['Memo','Teléfono','Jefe','Usuario'] as $op)
                                <option {{ $servicio->tipo_atencion == $op ? 'selected':'' }}>{{ $op }}</option>
                            @endforeach
                        </select>
                        <div id="bloqueMemoEdit" class="mt-2"
                             style="{{ $servicio->tipo_atencion === 'Memo' ? '' : 'display:none' }}">
                            <input type="text" name="num_memo" id="numMemoEdit" class="form-control"
                                   placeholder="Número o folio del memo" maxlength="100"
                                   value="{{ $servicio->num_memo }}">
                            <small class="text-muted">Número o folio del memo de referencia.</small>
                        </div>
                    </div>
                </td>
                <th>Departamento</th>
                <td>
                    <span class="vista">
                        {{ $departamentos->firstWhere('id_departamento', $servicio->id_departamento)?->nombre ?? 'No asignado' }}
                    </span>
                    <select name="id_departamento" class="form-select edicion">
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id_departamento }}"
                                {{ $dep->id_departamento == $servicio->id_departamento ? 'selected':'' }}>
                                {{ $dep->nombre }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
        </table>

        {{-- Tipo de equipo --}}
        <div class="section-title">Tipo de Equipo</div>
        <p class="vista">{{ $servicio->subtipo ?: '—' }}</p>
        <input name="subtipo" class="form-control edicion" value="{{ $servicio->subtipo }}">

        {{-- Descripción --}}
        <div class="section-title">Descripción del Servicio</div>
        <p class="vista">{{ $servicio->descripcion_servicio ?: 'Sin descripción.' }}</p>
        <textarea name="descripcion_servicio" class="form-control edicion" rows="3">{{ $servicio->descripcion_servicio }}</textarea>

        {{-- Detalles del equipo --}}
        <div class="section-title">Detalles del Equipo</div>
        <table class="table table-bordered">
            <tr>
                <th width="15%">Equipo</th>
                <td class="vista">{{ $servicio->equipo }}</td>
                <td class="edicion"><input name="equipo" class="form-control" value="{{ $servicio->equipo }}"></td>
                <th width="15%">Marca</th>
                <td class="vista">{{ $servicio->marca }}</td>
                <td class="edicion"><input name="marca" class="form-control" value="{{ $servicio->marca }}"></td>
            </tr>
            <tr>
                <th>Modelo</th>
                <td class="vista">{{ $servicio->modelo }}</td>
                <td class="edicion"><input name="modelo" class="form-control" value="{{ $servicio->modelo }}"></td>
                <th>No. Inventario</th>
                <td class="vista">{{ $servicio->numero_inventario }}</td>
                <td class="edicion"><input name="numero_inventario" class="form-control" value="{{ $servicio->numero_inventario }}"></td>
            </tr>
            <tr>
                <th>No. Serie</th>
                <td class="vista">{{ $servicio->numero_serie }}</td>
                <td class="edicion"><input name="numero_serie" class="form-control" value="{{ $servicio->numero_serie }}"></td>
                <th>Sistema Operativo</th>
                <td class="vista">{{ $servicio->sistema_operativo }}</td>
                <td class="edicion"><input name="sistema_operativo" class="form-control" value="{{ $servicio->sistema_operativo }}"></td>
            </tr>
            <tr>
                <th>Procesador</th>
                <td class="vista">{{ $servicio->procesador }}</td>
                <td class="edicion"><input name="procesador" class="form-control" value="{{ $servicio->procesador }}"></td>
                <th>RAM</th>
                <td class="vista">{{ $servicio->ram }}</td>
                <td class="edicion"><input name="ram" class="form-control" value="{{ $servicio->ram }}"></td>
            </tr>
            <tr>
                <th>Disco Duro</th>
                <td class="vista">{{ $servicio->disco_duro }}</td>
                <td class="edicion"><input name="disco_duro" class="form-control" value="{{ $servicio->disco_duro }}"></td>
                <th>Origen de Falla</th>
                <td class="vista">{{ $servicio->origen_falla }}</td>
                <td class="edicion">
                    <select name="origen_falla" class="form-select">
                        @foreach(['Desgaste natural','Mala operación','Otro'] as $op)
                            <option {{ $servicio->origen_falla==$op ? 'selected':'' }}>{{ $op }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>

        </table>

        {{-- Campos texto --}}
        @foreach([
            'diagnostico'        => 'Diagnóstico',
            'trabajo_realizado'  => 'Trabajo Realizado',
            'detalle_realizado'  => 'Detalle del Trabajo Realizado',
            'conclusion_servicio'=> 'Conclusión',
        ] as $campo => $label)
            <div class="section-title">{{ $label }}</div>
            <p class="vista">{{ $servicio->$campo ?: 'Sin información.' }}</p>
            <textarea name="{{ $campo }}" class="form-control edicion" rows="3">{{ $servicio->$campo }}</textarea>
        @endforeach

        {{-- Materiales --}}
        <div class="section-title">Materiales Utilizados</div>
        <table class="table table-bordered vista">
            <thead><tr><th>Material</th><th>Cantidad</th></tr></thead>
            <tbody>
            @forelse($materiales as $m)
                <tr><td>{{ $m->nombre }}</td><td>{{ $m->cantidad }}</td></tr>
            @empty
                <tr><td colspan="2" class="text-center">Sin materiales</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="edicion">
            <table class="table table-bordered" id="tabla-materiales">
                <thead><tr><th>Material</th><th>Cantidad</th><th>Acción</th></tr></thead>
                <tbody>
                @foreach($materiales as $i => $m)
                    <tr>
                        <td>
                            <select name="materiales[{{ $i }}][id_material]" class="form-select">
                                @foreach($catalogo_materiales as $mat)
                                    <option value="{{ $mat->id_material }}"
                                        {{ $mat->id_material == $m->id_material ? 'selected':'' }}>
                                        {{ $mat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="materiales[{{ $i }}][cantidad]" class="form-control" value="{{ $m->cantidad }}"></td>
                        <td><button type="button" class="btn btn-danger btn-sm eliminarFila">✕</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-primary btn-sm mt-2" id="btnAgregar">
                <i class="fas fa-plus me-1"></i> Agregar Material
            </button>
        </div>

        {{-- Observaciones --}}
        <div class="section-title">Observaciones</div>
        <p class="vista">{{ $servicio->observaciones ?: 'Ninguna.' }}</p>
        <textarea name="observaciones" class="form-control edicion" rows="2">{{ $servicio->observaciones }}</textarea>

        {{-- Firmas --}}
        <div class="section-title">Firmas de Conformidad</div>
        <table class="firmas">
            <tr>
                <td>
                    <strong>Usuario / Solicitante</strong><br>
                    <span class="vista">{{ $servicio->firma_usuario ?: '_________________' }}</span>
                    <input name="firma_usuario" id="firmaUsuarioEdit" class="form-control edicion" value="{{ $servicio->firma_usuario }}">
                </td>
                <td>
                    <strong>Responsable</strong><br>
                    <span class="vista">{{ $servicio->firma_tecnico ?: '_________________' }}</span>
                    <input name="firma_tecnico" class="form-control edicion" value="{{ $servicio->firma_tecnico }}">
                </td>
                <td>
                    <strong>Jefe de Área</strong><br>
                    <span class="vista">{{ $servicio->firma_jefe_area ?: '_________________' }}</span>
                    <input name="firma_jefe_area" id="firmaJefeEdit" class="form-control edicion" value="{{ $servicio->firma_jefe_area }}">
                </td>
            </tr>
        </table>

        <div id="guardarBtn" class="text-end mt-4" style="display:none;">
            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>

    <div class="footer">Sistema de Formatos Digitales SEMAHN 2026 — Generado el {{ date('d/m/Y H:i') }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── Activar modo edición ────────────────────────────────────────────────────
    function toggleEdicion() {
        document.querySelectorAll('.vista').forEach(v  => v.style.display = 'none');
        document.querySelectorAll('.edicion').forEach(e => e.style.display = 'block');
        document.getElementById('guardarBtn').style.display = 'block';

        // Si ya era Memo, mantener visible
        if (document.getElementById('tipoAtencionEdit').value === 'Memo') {
            document.getElementById('bloqueMemoEdit').style.display = 'block';
            document.getElementById('numMemoEdit').setAttribute('required', 'required');
        }
        // Si ya era Jefe, autocomplete firma usuario
        if (document.getElementById('tipoAtencionEdit').value === 'Jefe') {
            const firmaUser = document.getElementById('firmaUsuarioEdit');
            firmaUser.value    = document.getElementById('firmaJefeEdit').value;
            firmaUser.readOnly = true;
        }
    }

    // ── Lógica tipo de atención (Memo + Jefe) ──────────────────────────────────
    document.getElementById('tipoAtencionEdit').addEventListener('change', function () {
        const bloque    = document.getElementById('bloqueMemoEdit');
        const inputMemo = document.getElementById('numMemoEdit');
        const firmaUser = document.getElementById('firmaUsuarioEdit');
        const firmaJefe = document.getElementById('firmaJefeEdit');

        // Memo
        if (this.value === 'Memo') {
            bloque.style.display = 'block';
            inputMemo.setAttribute('required', 'required');
        } else {
            bloque.style.display = 'none';
            inputMemo.removeAttribute('required');
            inputMemo.value = '';
        }

        // Jefe → autocomplete firma solicitante
        if (this.value === 'Jefe') {
            firmaUser.value    = firmaJefe.value;
            firmaUser.readOnly = true;
        } else {
            if (firmaUser.readOnly) firmaUser.value = '';
            firmaUser.readOnly = false;
        }
    });

    // ── Materiales dinámiicos ───────────────────────────────────────────────────
    let contador = {{ count($materiales) }};
    document.getElementById('btnAgregar').addEventListener('click', function () {
        document.querySelector('#tabla-materiales tbody').insertAdjacentHTML('beforeend', `
        <tr>
            <td>
                <select name="materiales[${contador}][id_material]" class="form-select">
                    @foreach($catalogo_materiales as $mat)
        <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                    @endforeach
        </select>
    </td>
    <td><input type="number" name="materiales[${contador}][cantidad]" class="form-control" value="1"></td>
            <td><button type="button" class="btn btn-danger btn-sm eliminarFila">✕</button></td>
        </tr>`);
        contador++;
    });
    document.addEventListener('click', e => {
        if (e.target.closest('.eliminarFila')) e.target.closest('tr').remove();
    });
</script>
</body>
</html>
