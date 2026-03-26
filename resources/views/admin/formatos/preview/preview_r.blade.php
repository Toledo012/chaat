<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista previa - Formato R</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f8f9fa; padding:2rem; }
        .header { border-bottom:3px solid #399e91; padding-bottom:10px; margin-bottom:20px; }
        .header img { width:110px; }
        .titulo { text-align:center; font-weight:bold; color:#0a3622; text-transform:uppercase; margin-top:.5rem; }
        .subtitulo { text-align:center; font-size:13px; color:#6c757d; margin-bottom:1rem; }
        .section-title {
            background:#d1e7dd; color:#0a3622; font-weight:bold;
            padding:6px 10px; border-radius:4px; margin-top:1rem; margin-bottom:.4rem;
        }
        table { width:100%; border-collapse:collapse; }
        table td, table th { border:1px solid #dee2e6; padding:8px; vertical-align:top; }
        table th { background:#f8f9fa; width:22%; font-weight:600; }
        .bloque-descripcion {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 12px;
            min-height: 120px;
            background: #fff;
            white-space: pre-wrap;
        }
        .firmas td { border:none; text-align:center; padding-top:40px; }
        .footer {
            text-align:center; font-size:12px; color:#6c757d;
            border-top:2px solid #ccc; margin-top:2rem; padding-top:10px;
        }
        .edicion { display:none; }
        .modo-edicion .vista { display:none; }
        .modo-edicion .edicion { display:block; }
    </style>
</head>
<body>
<div class="container bg-white shadow p-4 rounded">

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> Volver a Formatos
        </a>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" onclick="toggleEdicion()">
                <i class="fa-solid fa-pen-to-square"></i> Editar
            </button>
            <a href="{{ route('admin.formatos.r.pdf', $servicio->id_servicio) }}"
               target="_blank"
               class="btn btn-danger">
                <i class="fa fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    <form id="formEdicion"
          method="POST"
          action="{{ route('admin.formatos.update', ['R', $servicio->id_servicio]) }}">
        @csrf

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

        <h5 class="titulo">Formato de Recepción</h5>
        <p class="subtitulo">Registro de artículos, equipos y materiales recibidos</p>

        <div class="section-title">Datos Generales</div>
        <table class="table table-bordered">
            <tr>
                <th>Folio</th>
                <td>{{ $servicio->folio ?? 'ID-'.$servicio->id_servicio }}</td>
                <th>Fecha</th>
                <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Tipo de Formato</th>
                <td>R</td>
                <th>Departamento</th>
                <td>
                    <span class="vista">
                        {{ $departamentos->firstWhere('id_departamento', $servicio->id_departamento)?->nombre ?? 'No asignado' }}
                    </span>
                    <select name="id_departamento" class="form-select edicion">
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id_departamento }}"
                                {{ $dep->id_departamento == $servicio->id_departamento ? 'selected' : '' }}>
                                {{ $dep->nombre }}
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
        </table>

        <div class="section-title">Descripción de Artículos / Equipos Recibidos</div>

        <div class="vista bloque-descripcion mb-2">
            {{ $servicio->descripcion ?? 'Sin descripción registrada.' }}
        </div>

        <textarea name="descripcion" class="form-control edicion mb-2" rows="6">{{ $servicio->descripcion }}</textarea>

        <div class="section-title">Firmas</div>
        <table class="table table-bordered firmas">
            <tr>
                <td>
                    <div class="vista">
                        <strong>Usuario / Solicitante</strong><br><br>
                        {{ $servicio->firma_usuario ?? 'Sin firma' }}
                    </div>
                    <div class="edicion">
                        <label class="form-label">Usuario / Solicitante</label>
                        <input type="text" name="firma_usuario" class="form-control"
                               value="{{ $servicio->firma_usuario }}">
                    </div>
                </td>
                <td>
                    <div class="vista">
                        <strong>Técnico Responsable</strong><br><br>
                        {{ $servicio->firma_tecnico ?? 'Sin firma' }}
                    </div>
                    <div class="edicion">
                        <label class="form-label">Técnico Responsable</label>
                        <input type="text" name="firma_tecnico" class="form-control"
                               value="{{ $servicio->firma_tecnico }}">
                    </div>
                </td>
            </tr>
        </table>

        <div class="text-end mt-3 edicion">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Guardar cambios
            </button>
        </div>
    </form>

    <div class="footer">
        Generado desde el Sistema de Formatos Digitales — SEMAHN
    </div>
</div>

<script>
    function toggleEdicion() {
        document.getElementById('formEdicion').classList.toggle('modo-edicion');
    }
</script>
</body>
</html>
