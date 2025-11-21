<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista previa - Formato A</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: "Arial", sans-serif;
            font-size: 14px;
            background-color: #f8f9fa;
            padding: 2rem;
        }
        .header {
            border-bottom: 3px solid #157347;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            width: 110px;
        }
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

        /* Textarea oculto por defecto */
        .edicion { display: none; }
    </style>
</head>

<body>

<div class="container bg-white shadow p-4 rounded">

    {{-- BOTÓN EDITAR --}}
    <div class="mb-3 text-end">
        <button type="button" class="btn btn-success" onclick="toggleEdicion()">
            <i class="fa-solid fa-pen-to-square"></i> Editar Formato
        </button>
    </div>

    {{-- FORMULARIO PARA ACTUALIZAR --}}
    <form method="POST" action="{{ route('admin.formatos.update', ['A', $servicio->id_servicio]) }}">
        @csrf

        {{-- ENCABEZADO INSTITUCIONAL --}}
        <div class="row align-items-center header">
            <div class="col-3 text-center">
                <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo SEMAHN">
            </div>
            <div class="col-9 text-center">
                <h5>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h5>
                <p class="mb-0">UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA</p>
                <small><em>"2025, Año de Rosario Castellanos Figueroa"</em></small>
            </div>
        </div>

        {{-- TÍTULO --}}
        <h5 class="titulo">Formato A - Soporte y Desarrollo</h5>
        <p class="subtitulo">Atención de servicios de soporte técnico o desarrollo institucional</p>

        {{-- DATOS GENERALES --}}
        <div class="section-title">Datos del Servicio</div>
        <table class="table table-bordered">
            <tr>
                <th width="25%">Folio</th>
                <td>{{ $servicio->folio }}</td>
                <th width="25%">Fecha</th>
                <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Tipo de Formato</th>
                <td colspan="3">A</td>
            </tr>
        </table>

        {{-- PETICIÓN --}}
        <div class="section-title">Petición del Servicio</div>
        <p class="vista">{{ $servicio->peticion ?? 'Sin descripción registrada.' }}</p>
        <textarea name="peticion" class="form-control edicion">{{ $servicio->peticion }}</textarea>

        {{-- TRABAJO REALIZADO --}}
        <div class="section-title">Trabajo Realizado</div>
        <p class="vista">{{ $servicio->trabajo_realizado ?? 'No especificado' }}</p>
        <textarea name="trabajo_realizado" class="form-control edicion">{{ $servicio->trabajo_realizado }}</textarea>

        {{-- CONCLUSIÓN --}}
        <div class="section-title">Conclusión del Servicio</div>
        <p class="vista">{{ $servicio->conclusion_servicio ?? 'Sin datos' }}</p>
        <textarea name="conclusion_servicio" class="form-control edicion">{{ $servicio->conclusion_servicio }}</textarea>

        {{-- OBSERVACIONES --}}
        <div class="section-title">Observaciones</div>
        <p class="vista">{{ $servicio->observaciones ?? 'Ninguna' }}</p>
        <textarea name="observaciones" class="form-control edicion">{{ $servicio->observaciones }}</textarea>

        {{-- FIRMAS --}}
        <div class="section-title">Firmas de Conformidad</div>
        <table class="firmas" width="100%">
            <tr>
                <td>
                    <strong>Usuario Solicitante</strong><br>
                    <span class="vista">{{ $servicio->firma_usuario ?? '___________________' }}</span>
                    <input type="text" name="firma_usuario" class="form-control edicion" value="{{ $servicio->firma_usuario }}">
                </td>
                <td>
                    <strong>Realiza el Servicio</strong><br>
                    <span class="vista">{{ $servicio->firma_tecnico ?? '___________________' }}</span>
                    <input type="text" name="firma_tecnico" class="form-control edicion" value="{{ $servicio->firma_tecnico }}">
                </td>
                <td>
                    <strong>Jefe de Área</strong><br>
                    <span class="vista">{{ $servicio->firma_jefe_area ?? '___________________' }}</span>
                    <input type="text" name="firma_jefe_area" class="form-control edicion" value="{{ $servicio->firma_jefe_area }}">
                </td>
            </tr>
        </table>

        {{-- BOTÓN GUARDAR --}}
        <div id="guardarBtn" class="text-end mt-4" style="display:none;">
            <button type="submit" class="btn btn-primary">
                Guardar Cambios
            </button>
        </div>

    </form>

    {{-- PIE --}}
    <div class="footer">
        <p>Secretaría de Medio Ambiente e Historia Natural - Unidad de Apoyo Administrativo</p>
        <p>Generado desde el Sistema de Formatos Digitales</p>
    </div>

                    <div class="text-end">
                    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>

</div>



{{-- SCRIPT PARA CAMBIAR A MODO EDICIÓN --}}
<script>
function toggleEdicion() {
    document.querySelectorAll('.vista').forEach(v => v.style.display = 'none');
    document.querySelectorAll('.edicion').forEach(e => e.style.display = 'block');
    document.getElementById('guardarBtn').style.display = 'block';
}
</script>

</body>
</html>
