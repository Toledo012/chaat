<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista previa - Formato D</title>
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
        table th { background:#f8f9fa; width:25%; font-weight:600; }
        .firmas-row { display:flex; justify-content:space-around; margin-top:40px; text-align:center; }
        .firma-bloque { flex:1; }
        .firma-linea { border-top:1px solid #333; margin:0 20px 8px; }
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
            <a href="{{ route('admin.formatos.d.pdf', $servicio->id_servicio) }}"
               target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.formatos.update', ['D', $servicio->id_servicio]) }}">
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

        <h5 class="titulo">Formato D — Mantenimiento Equipos Personales</h5>
        <p class="subtitulo">Entrega y recepción de equipo personal para mantenimiento</p>

        {{-- Datos generales con tipo_atencion --}}
        <div class="section-title">Datos del Servicio</div>
        <table class="table table-bordered">
            <tr>
                <th>Fecha</th>
                <td>
                    <span class="vista">{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</span>
                    <input type="date" name="fecha" class="form-control edicion" value="{{ $servicio->fecha }}">
                </td>
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
            </tr>
        </table>

        {{-- Texto introductorio --}}
        <p class="mt-3 vista">
            El C. <strong>{{ $servicio->otorgante }}</strong> entrega el equipo con las siguientes características:
        </p>
        <div class="edicion mb-3">
            <label class="form-label fw-semibold">Nombre del Otorgante</label>
            <input type="text" name="otorgante" class="form-control" value="{{ $servicio->otorgante }}">
        </div>

        {{-- Datos del equipo --}}
        <div class="section-title">Datos del Equipo</div>
        <table class="table table-bordered">
            <tr>
                <th>Equipo</th>
                <td class="vista">{{ $servicio->equipo }}</td>
                <td class="edicion"><input name="equipo" class="form-control" value="{{ $servicio->equipo }}"></td>
                <th>Marca</th>
                <td class="vista">{{ $servicio->marca }}</td>
                <td class="edicion"><input name="marca" class="form-control" value="{{ $servicio->marca }}"></td>
            </tr>
            <tr>
                <th>Modelo</th>
                <td class="vista">{{ $servicio->modelo }}</td>
                <td class="edicion"><input name="modelo" class="form-control" value="{{ $servicio->modelo }}"></td>
                <th>No. Serie</th>
                <td class="vista">{{ $servicio->serie }}</td>
                <td class="edicion"><input name="serie" class="form-control" value="{{ $servicio->serie }}"></td>
            </tr>
        </table>

        {{-- Texto oficial --}}
        <p class="vista mt-3">
            Sirva el presente formato como comprobante de entrega del equipo mencionado, que pertenece al
            C. <strong>{{ $servicio->otorgante }}</strong>, al personal del Área de Informática de la
            Secretaría de Medio Ambiente e Historia Natural, que se compromete a realizar el servicio de
            manera cuidadosa y profesional.
        </p>

        {{-- Receptor --}}
        <div class="section-title">Receptor</div>
        <p class="vista">{{ $servicio->receptor }}</p>
        <div class="edicion">
            <label class="form-label fw-semibold">Nombre del Receptor</label>
            <input type="text" name="receptor" class="form-control" value="{{ $servicio->receptor }}">
        </div>

        {{-- Observaciones --}}
        <div class="section-title">Observaciones</div>
        <p class="vista">{{ $servicio->observaciones ?: 'Ninguna.' }}</p>
        <textarea name="observaciones" class="form-control edicion" rows="3">{{ $servicio->observaciones }}</textarea>

        {{-- Firmas --}}
        <div class="section-title">Firmas</div>
        <div class="firmas-row mt-4">
            <div class="firma-bloque">
                <div class="firma-linea"></div>
                <strong>OTORGANTE</strong><br>
                <span class="vista">{{ $servicio->otorgante }}</span>
            </div>
            <div class="firma-bloque">
                <div class="firma-linea"></div>
                <strong>RECEPTOR</strong><br>
                <span class="vista">{{ $servicio->receptor }}</span>
            </div>
            <div class="firma-bloque">
                <div class="firma-linea"></div>
                <strong>JEFE DE ÁREA</strong><br>
                <span class="vista">{{ $servicio->firma_jefe_area ?: '_________________' }}</span>
                <input class="form-control edicion mt-2" name="firma_jefe_area" id="firmaJefeEdit"
                       value="{{ $servicio->firma_jefe_area }}">
            </div>
        </div>

        <div id="guardarBtn" class="text-end mt-4" style="display:none;">
            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>

    <div class="footer">Sistema de Formatos Digitales SEMAHN 2026 — Generado el {{ date('d/m/Y H:i') }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleEdicion() {
        document.querySelectorAll('.vista').forEach(v  => v.style.display = 'none');
        document.querySelectorAll('.edicion').forEach(e => e.style.display = 'block');
        document.getElementById('guardarBtn').style.display = 'block';
        if (document.getElementById('tipoAtencionEdit').value === 'Memo') {
            document.getElementById('bloqueMemoEdit').style.display = 'block';
            document.getElementById('numMemoEdit').setAttribute('required','required');
        }
    }

    document.getElementById('tipoAtencionEdit').addEventListener('change', function () {
        const bloque    = document.getElementById('bloqueMemoEdit');
        const inputMemo = document.getElementById('numMemoEdit');

        bloque.style.display = (this.value === 'Memo') ? 'block' : 'none';
        if (this.value === 'Memo') inputMemo.setAttribute('required','required');
        else { inputMemo.removeAttribute('required'); inputMemo.value = ''; }
    });
</script>
</body>
</html>
