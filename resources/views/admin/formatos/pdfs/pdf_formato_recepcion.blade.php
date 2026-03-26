<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato Recepción - PDF</title>

    <style>
        @page { margin: 1.2cm; }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.35;
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
            width: 55px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .titulo {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            margin: 6px 0 2px 0;
            text-align: center;
        }

        .subtitulo {
            font-size: 9px;
            text-align: center;
            margin-bottom: 8px;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px 7px;
            vertical-align: top;
        }

        th {
            background: #e8e8e8;
            text-align: left;
            width: 22%;
            font-size: 10px;
        }

        .section-title {
            font-weight: bold;
            background: #d1d1d1;
            padding: 4px 7px;
            margin-top: 10px;
            margin-bottom: 2px;
            border: 1px solid #000;
            font-size: 10px;
            text-transform: uppercase;
        }

        .bloque-descripcion {
            border: 1px solid #000;
            padding: 10px;
            min-height: 85px;
            white-space: pre-wrap;
            font-size: 10px;
            line-height: 1.5;
        }

        .firmas {
            margin-top: 30px;
        }

        .firmas td {
            border: none;
            text-align: center;
            padding-top: 10px;
            font-size: 10px;
            width: 50%;
        }

        .linea-firma {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto 4px auto;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ccc;
            margin-top: 12px;
            padding-top: 4px;
        }

        .corte {
            border-top: 1px dashed #777;
            margin: 12px 0;
        }
    </style>
</head>

<body>
@php
    $nombreDepto = $departamentos->firstWhere('id_departamento', $servicio->id_departamento)?->nombre ?? 'No asignado';
@endphp

@for($i = 1; $i <= 2; $i++)
    <div class="formato">
        <div class="header">
            <img src="{{ public_path('images/logo_semahn2.png') }}" alt="SEMAHN">
            <strong>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</strong><br>
            UNIDAD DE APOYO ADMINISTRATIVO — ÁREA DE INFORMÁTICA<br>
            <em>"2025, Año de Rosario Castellanos Figueroa"</em>
        </div>

        <div class="titulo">Formato de Recepción</div>
        <div class="subtitulo">Registro de artículos, equipos y materiales recibidos</div>

        <div class="section-title">Datos Generales</div>
        <table>
            <tr>
                <th>Folio</th>
                <td>{{ $servicio->folio ?? 'Sin folio' }}</td>
                <th>Fecha</th>
                <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Tipo de Formato</th>
                <td>R</td>
                <th>Departamento</th>
                <td>{{ $nombreDepto }}</td>
            </tr>
        </table>

        <div class="section-title">Descripción de Artículos / Equipos Recibidos</div>
        <div class="bloque-descripcion">
            {{ $servicio->descripcion ?? 'Sin descripción registrada.' }}
        </div>

        <table class="firmas">
            <tr>
                <td>
                    <div class="linea-firma"></div>
                    <strong>Usuario / Entrega</strong><br>
                    {{ $servicio->firma_usuario ?? '' }}
                </td>
                <td>
                    <div class="linea-firma"></div>
                    <strong>Usuario Responsable</strong><br>
                    {{ $servicio->firma_tecnico ?? '' }}
                </td>
            </tr>
        </table>

        <div class="footer">
            Generado desde el Sistema de Formatos Digitales — SEMAHN | {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    @if($i === 1)
        <div class="corte"></div>
    @endif
@endfor
</body>
</html>
