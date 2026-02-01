<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato C - Redes y Telefonía</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            font-size: 12px;
            color:#000;
            margin: 20px;
        }

        .header{
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header img{
            width: 90px;
            float: left;
        }

        .header .text{
            text-align: center;
        }

        .titulo{
            text-align:center;
            font-weight:bold;
            font-size:14px;
            margin:10px 0 5px;
            text-transform: uppercase;
        }

        .subtitulo{
            text-align:center;
            font-size:12px;
            margin-bottom:10px;
        }

        .section-title{
            font-weight:bold;
            background:#e2e3e5;
            padding:4px;
            margin-top:8px;
            font-size:12px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:4px;
        }

        th, td{
            border:1px solid #000;
            padding:5px;
            vertical-align:top;
        }

        .firmas td{
            border:none;
            text-align:center;
            padding-top:30px;
        }

        .footer{
            text-align:center;
            font-size:11px;
            margin-top:12px;
            border-top:1px solid #000;
            padding-top:5px;
        }
    </style>
</head>
<body>

{{-- ================= ENCABEZADO ================= --}}
<div class="header">
    <img src="{{ public_path('images/logo_semahn2.png') }}">
    <div class="text">
        <strong>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</strong><br>
        UNIDAD DE APOYO ADMINISTRATIVO – ÁREA DE INFORMÁTICA<br>
        <em>"2025, Año de Rosario Castellanos Figueroa"</em>
    </div>
    <div style="clear:both"></div>
</div>

<div class="titulo">Formato C - Redes / Telefonía</div>
<div class="subtitulo">Atención de servicios de redes o telefonía institucional</div>

{{-- ================= DATOS DEL SERVICIO ================= --}}
<div class="section-title">Datos del Servicio</div>
<table>
    <tr>
 <th width="25%">Folio</th>
                <td>{{ $servicio->folio }}</td>
        <th width="20%">Fecha</th>
        <td width="30%">{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <th>Departamento</th>
        <td colspan="3">
            {{ $servicio->departamento_nombre ?? 'No especificado' }}
        </td>
    </tr>
</table>

{{-- ================= DATOS TÉCNICOS ================= --}}
<div class="section-title">Datos Técnicos</div>
<table>
    <tr>
        <th width="25%">Tipo de Red</th>
        <td width="25%">{{ $servicio->tipo_red }}</td>
        <th width="25%">Tipo de Servicio</th>
        <td width="25%">{{ $servicio->tipo_servicio }}</td>
    </tr>
</table>

{{-- ================= DESCRIPCIÓN ================= --}}
<div class="section-title">Descripción del Servicio</div>
<p>{{ $servicio->descripcion_servicio ?? 'Sin descripción.' }}</p>

{{-- ================= DIAGNÓSTICO ================= --}}
<div class="section-title">Diagnóstico</div>
<p>{{ $servicio->diagnostico ?? 'No especificado.' }}</p>

{{-- ================= TRABAJO REALIZADO ================= --}}
<div class="section-title">Trabajo Realizado</div>
<p>{{ $servicio->trabajo_realizado ?? 'No especificado.' }}</p>

{{-- ================= MATERIALES ================= --}}
<div class="section-title">Materiales Utilizados</div>
<table>
    <thead>
        <tr>
            <th>Material</th>
            <th width="20%">Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materiales as $m)
            <tr>
                <td>{{ $m->nombre }}</td>
                <td style="text-align:center">{{ $m->cantidad }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="text-align:center">Sin materiales</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- ================= OBSERVACIONES ================= --}}
<div class="section-title">Observaciones</div>
<p>{{ $servicio->observaciones ?? 'Ninguna.' }}</p>

{{-- ================= FIRMAS ================= --}}
<table class="firmas" width="100%">
    <tr>
        <td>
            ___________________________<br>
            <strong>Usuario</strong><br>
            {{ $servicio->firma_usuario ?? '' }}
        </td>
        <td>
            ___________________________<br>
            <strong>Técnico</strong><br>
            {{ $servicio->firma_tecnico ?? '' }}
        </td>
        <td>
            ___________________________<br>
            <strong>Jefe de Área</strong><br>
            {{ $servicio->firma_jefe_area ?? '' }}
        </td>
    </tr>
</table>

<div class="footer">
    Generado desde el Sistema de Formatos Digitales
</div>

</body>
</html>
