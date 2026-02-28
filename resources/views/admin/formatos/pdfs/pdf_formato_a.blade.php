<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato A - PDF</title>

    <style>
        @page {
            margin: 2cm; /* Margen estándar para impresión */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
        }

        .formato {
            width: 100%;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
            position: relative;
        }

        .header img {
            width: 60px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .titulo {
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            margin: 5px 0;
            text-align: center;
        }

        .subtitulo {
            font-size: 11px;
            margin-bottom: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        th {
            width: 20%;
            background: #f1f1f1;
            text-align: left;
        }

        .section-title {
            font-weight: bold;
            background: #e2e3e5;
            padding: 4px;
            margin-top: 10px;
            margin-bottom: 2px;
            border: 1px solid #000;
        }

        .bloque {
            border: 1px solid #000;
            padding: 8px;
            min-height: 40px;
            margin-bottom: 5px;
        }

        .firmas {
            margin-top: 30px;
        }

        .firmas td {
            border: none;
            text-align: center;
            padding-top: 20px;
            font-size: 10px;
            width: 33%;
        }
    </style>
</head>

<body>

<div class="formato">
    <div class="header">
        <img src="{{ public_path('images/logo_semahn2.png') }}">
        <strong>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</strong><br>
        UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA<br>
        <em>"2025, Año de Rosario Castellanos Figueroa"</em>
    </div>

    <div class="titulo">Formato A - Soporte y Desarrollo</div>
    <div class="subtitulo">Atención de servicios de soporte técnico o desarrollo institucional</div>

    <table>
        <tr>
            <th>Folio / ID</th>
            <td>{{ $servicio->folio ?? $servicio->id_servicio }}</td>
            <th>Fecha</th>
            <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Departamento</th>
            <td colspan="3">
                {{ $departamentos->firstWhere('id_departamento', $servicio->id_departamento)?->nombre ?? 'No asignado' }}
            </td>
        </tr>
    </table>

    <div class="section-title">Clasificación del Servicio</div>
    <table>
        <tr>
            <th>Subtipo</th>
            <td>{{ $servicio->subtipo ?? '—' }}</td>
            <th>Tipo de Atención</th>
            <td>{{ $servicio->tipo_atencion ?? '—' }}</td>
        </tr>
        <tr>
            <th>Tipo de Servicio</th>
            <td colspan="3">{{ $servicio->tipo_servicio ?? '—' }}</td>
        </tr>
    </table>

    <div class="section-title">Petición del Servicio</div>
    <div class="bloque">{{ $servicio->peticion ?? '—' }}</div>

    <div class="section-title">Trabajo Realizado</div>
    <div class="bloque">{{ $servicio->trabajo_realizado ?? '—' }}</div>

    <div class="section-title">Detalle del Trabajo Realizado</div>
    <div class="bloque">{{ $servicio->detalle_realizado ?? '—' }}</div>

    <div class="section-title">Conclusión del Servicio</div>
    <div class="bloque">{{ $servicio->conclusion_servicio ?? '—' }}</div>

    <div class="section-title">Observaciones</div>
    <div class="bloque">{{ $servicio->observaciones ?? '—' }}</div>

    <table class="firmas">
        <tr>
            <td>
                _________________________<br>
                <strong>Usuario Solicitante</strong><br>
                {{ $servicio->firma_usuario }}
            </td>
            <td>
                _________________________<br>
                <strong>Técnico</strong><br>
                {{ $servicio->firma_tecnico }}
            </td>
            <td>
                _________________________<br>
                <strong>Jefe de Área</strong><br>
                {{ $servicio->firma_jefe_area }}
            </td>
        </tr>
    </table>
</div>

</body>
</html>
