<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista previa - Formato A</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            background-color: #f8f9fa;
            padding: 2rem;
        }
        .header {
            border-bottom: 3px solid #157347;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img { width: 110px; }
        .titulo {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            color: #0a3622;
        }
        .subtitulo {
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .section-title {
            background-color: #d1e7dd;
            color: #0a3622;
            font-weight: bold;
            padding: 6px;
            border-radius: 4px;
            margin-top: 1rem;
            margin-bottom: .5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: top;
        }
        .firmas td {
            border: none;
            text-align: center;
            padding-top: 30px;
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #6c757d;
            border-top: 2px solid #ccc;
            margin-top: 2rem;
            padding-top: 10px;
        }
        .edicion { display: none; }
    </style>
</head>

<body>
<div class="container bg-white shadow p-4 rounded">

    {{-- BOTONES --}}
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">
            ← Volver a Formatos
        </a>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" onclick="toggleEdicion()">✏️ Editar</button>
            <a href="{{ route('admin.formatos.a.pdf', $servicio->id_servicio) }}"
               target="_blank"
               class="btn btn-danger">📄 PDF</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.formatos.update', ['A', $servicio->id_servicio]) }}">
        @csrf

        {{-- ENCABEZADO --}}
        <div class="row align-items-center header">
            <div class="col-3 text-center">
                <img src="{{ asset('images/logo_semahn2.png') }}">
            </div>
            <div class="col-9 text-center">
                <h5>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h5>
                <p class="mb-0">UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA</p>
                <small><em>"2025, Año de Rosario Castellanos Figueroa"</em></small>
            </div>
        </div>

        <h5 class="titulo">Formato A - Soporte y Desarrollo</h5>
        <p class="subtitulo">Atención de servicios de soporte técnico o desarrollo institucional</p>

        {{-- DATOS GENERALES --}}
        <div class="section-title">Datos Generales</div>
        <table>
            <tr>
                <th width="25%">Folio</th>
                <td>{{ $servicio->folio }}</td>
                <th width="25%">Fecha</th>
                <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Tipo de Formato</th>
                <td>A</td>
        <th>Departamento</th>
        <td colspan="3">
            <span class="vista">
                {{ $departamentos->firstWhere('id_departamento', $servicio->id_departamento)?->nombre ?? 'No asignado' }}
            </span>
            <select name="id_departamento" class="form-select edicion">
                @foreach($departamentos as $dep)
                    <option value="{{ $dep->id_departamento }}" {{ $dep->id_departamento == $servicio->id_departamento ? 'selected' : '' }}>
                        {{ $dep->nombre }}
                    </option>
                @endforeach
            </select>
        </td>
    </tr>
</table>

        {{-- CLASIFICACIÓN --}}
        <div class="section-title">Clasificación del Servicio</div>
        <table>
            <tr>
                <th>Subtipo</th>
                <td>
                    <span class="vista">{{ $servicio->subtipo }}</span>
                    <select name="subtipo" class="form-select edicion">
                        @foreach(['Desarrollo','Soporte'] as $op)
                            <option {{ $servicio->subtipo == $op ? 'selected' : '' }}>{{ $op }}</option>
                        @endforeach
                    </select>
                </td>
                <th>Tipo de Atención</th>
                <td>
                    <span class="vista">{{ $servicio->tipo_atencion }}</span>
                    <select name="tipo_atencion" class="form-select edicion">
                        @foreach(['Memo','Teléfono','Jefe','Usuario'] as $op)
                            <option {{ $servicio->tipo_atencion == $op ? 'selected' : '' }}>{{ $op }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <th>Tipo de Servicio</th>
                <td>
                    <span class="vista">{{ $servicio->tipo_servicio }}</span>
                    <select name="tipo_servicio" class="form-select edicion">
                        @foreach(['Equipos','Redes LAN/WAN','Antivirus','Software'] as $op)
                            <option {{ $servicio->tipo_servicio == $op ? 'selected' : '' }}>{{ $op }}</option>
                        @endforeach
                    </select>
                </td>
                <th>Conclusión</th>
                <td>
                    <span class="vista">{{ $servicio->conclusion_servicio }}</span>
                    <select name="conclusion_servicio" class="form-select edicion">
                        @foreach(['Terminado','En proceso'] as $op)
                            <option {{ $servicio->conclusion_servicio == $op ? 'selected' : '' }}>{{ $op }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
        </table>

        {{-- TEXTOS --}}
        @foreach([
            'peticion' => 'Petición del Servicio',
            'trabajo_realizado' => 'Trabajo Realizado',
            'detalle_realizado' => 'Detalle del Trabajo Realizado',
            'observaciones' => 'Observaciones'
        ] as $campo => $label)
            <div class="section-title">{{ $label }}</div>
            <p class="vista">{{ $servicio->$campo ?: 'Sin información registrada.' }}</p>
            <textarea name="{{ $campo }}" class="form-control edicion">{{ $servicio->$campo }}</textarea>
        @endforeach

        {{-- FIRMAS --}}
        <div class="section-title">Firmas de Conformidad</div>
        <table class="firmas">
            <tr>
                <td>
                    <strong>Solicitante</strong><br>
                    <span class="vista">{{ $servicio->firma_usuario ?: '_________________' }}</span>
                    <input name="firma_usuario" class="form-control edicion" value="{{ $servicio->firma_usuario }}">
                </td>
                <td>
                    <strong>Técnico</strong><br>
                    <span class="vista">{{ $servicio->firma_tecnico ?: '_________________' }}</span>
                    <input name="firma_tecnico" class="form-control edicion" value="{{ $servicio->firma_tecnico }}">
                </td>
                <td>
                    <strong>Jefe de Área</strong><br>
                    <span class="vista">{{ $servicio->firma_jefe_area ?: '_________________' }}</span>
                    <input name="firma_jefe_area" class="form-control edicion" value="{{ $servicio->firma_jefe_area }}">
                </td>
            </tr>
        </table>

        {{-- BOTONES GUARDAR --}}
        <div id="guardarBtn" class="text-end mt-4" style="display:none;">
            <button type="button" class="btn btn-outline-secondary me-2" onclick="location.reload()">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>

    <div class="footer">
        Secretaría de Medio Ambiente e Historia Natural – Sistema de Formatos Digitales
    </div>
</div>

<script>
function toggleEdicion() {
    document.querySelectorAll('.vista').forEach(v => v.style.display = 'none');
    document.querySelectorAll('.edicion').forEach(e => e.style.display = 'block');
    document.getElementById('guardarBtn').style.display = 'block';
}
</script>
</body>
</html>
    