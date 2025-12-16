<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato A - PDF</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 15px;
        }

        .formato {
            height: 48%;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 6px;
            position: relative;
        }

        .header img {
            width: 80px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .titulo {
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            margin: 4px 0;
        }

        .subtitulo {
            font-size: 11px;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }

        .section-title {
            font-weight: bold;
            background: #e2e3e5;
            padding: 3px;
            margin-top: 5px;
            margin-bottom: 2px;
        }

        .firmas td {
            border: none;
            text-align: center;
            padding-top: 18px;
            font-size: 10px;
        }

        .corte {
            border-top: 1px dashed #000;
            margin: 8px 0;
            text-align: center;
            font-size: 9px;
        }
    </style>
</head>

<body>

{{-- ================== FORMATO SUPERIOR ================== --}}
<div class="formato">

    <div class="header">
        <img src="{{ public_path('images/logo_semahn2.png') }}">
        <strong>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</strong><br>
        UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA<br>
        <em>"2025, Año de Rosario Castellanos Figueroa"</em>
    </div>

    <div class="titulo">Formato A - Soporte y Desarrollo</div>
    <div class="subtitulo">Atención de servicios de soporte técnico o desarrollo institucional</div>

    <div class="section-title">Datos del Servicio</div>
    <table>
        <tr>
            <th>Folio</th>
            <td>{{ $servicio->folio ?? 'N/A' }}</td>
            <th>Fecha</th>
            <td>{{ \Carbon\Carbon::parse($servicio->fecha ?? now())->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="section-title">Petición del Servicio</div>
    <p>{{ $servicio->peticion ?? 'Sin descripción registrada.' }}</p>

    <div class="section-title">Trabajo Realizado</div>
    <p>{{ $servicio->trabajo_realizado ?? 'No se especifica.' }}</p>

    <div class="section-title">Detalle Realizado</div>
    <p>{{ $servicio->detalle_realizado ?? 'No se especifica.' }}</p>

    <div class="section-title">Conclusión del Servicio</div>
    <p>{{ $servicio->conclusion_servicio ?? 'Sin datos.' }}</p>

    <div class="section-title">Observaciones</div>
    <p>{{ $servicio->observaciones ?? 'Ninguna.' }}</p>

    <table class="firmas" width="100%">
        <tr>
            <td>
                _________________________<br>
                Usuario Solicitante<br>
                {{ $servicio->firma_usuario ?? '' }}
            </td>
            <td>
                _________________________<br>
                Realiza el Servicio<br>
                {{ $servicio->firma_tecnico ?? '' }}
            </td>
            <td>
                _________________________<br>
                Jefe de Área<br>
                {{ $servicio->firma_jefe_area ?? '' }}
            </td>
        </tr>
    </table>
</div>

{{-- ================== LÍNEA DE CORTE ================== --}}
<div class="corte">✂︎ Corte aquí</div>

{{-- ================== FORMATO INFERIOR (COPIA) ================== --}}
<div class="formato">

    <div class="header">
        <img src="{{ public_path('images/logo_semahn2.png') }}">
        <strong>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</strong><br>
        UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA<br>
        <em>"2025, Año de Rosario Castellanos Figueroa"</em>
    </div>

    <div class="titulo">Formato A - Soporte y Desarrollo</div>
    <div class="subtitulo">Atención de servicios de soporte técnico o desarrollo institucional</div>

    <div class="section-title">Datos del Servicio</div>
    <table>
        <tr>
            <th>Folio</th>
            <td>{{ $servicio->folio ?? 'N/A' }}</td>
            <th>Fecha</th>
            <td>{{ \Carbon\Carbon::parse($servicio->fecha ?? now())->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="section-title">Petición del Servicio</div>
    <p>{{ $servicio->peticion ?? 'Sin descripción registrada.' }}</p>

    <div class="section-title">Trabajo Realizado</div>
    <p>{{ $servicio->trabajo_realizado ?? 'No se especifica.' }}</p>

    <div class="section-title">Detalle Realizado</div>
    <p>{{ $servicio->detalle_realizado ?? 'No se especifica.' }}</p>

    <div class="section-title">Conclusión del Servicio</div>
    <p>{{ $servicio->conclusion_servicio ?? 'Sin datos.' }}</p>

    <div class="section-title">Observaciones</div>
    <p>{{ $servicio->observaciones ?? 'Ninguna.' }}</p>

    <table class="firmas" width="100%">
        <tr>
            <td>
                _________________________<br>
                Usuario Solicitante<br>
                {{ $servicio->firma_usuario ?? '' }}
            </td>
            <td>
                _________________________<br>
                Realiza el Servicio<br>
                {{ $servicio->firma_tecnico ?? '' }}
            </td>
            <td>
                _________________________<br>
                Jefe de Área<br>
                {{ $servicio->firma_jefe_area ?? '' }}
            </td>
        </tr>
    </table>
</div>

</body>
</html>
