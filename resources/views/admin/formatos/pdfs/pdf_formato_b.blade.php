<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato B - {{ $servicio->folio }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .header {
            border-bottom: 2px solid #157347;
            margin-bottom: 8px;
            padding-bottom: 6px;
        }

        .header img {
            width: 90px;
        }

        .titulo {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            margin: 6px 0;
            text-transform: uppercase;
        }

        .section {
            background-color: #d1e7dd;
            font-weight: bold;
            padding: 4px;
            margin-top: 6px;
            margin-bottom: 2px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            width: 18%;
        }

        .texto {
            min-height: 22px;
        }

        .firmas td {
            border: none;
            text-align: center;
            padding-top: 20px;
        }

        .footer {
            border-top: 1px solid #999;
            text-align: center;
            font-size: 10px;
            margin-top: 6px;
            padding-top: 4px;
        }
    </style>
</head>

<body>

{{-- ================= ENCABEZADO ================= --}}
<div class="header">
    <table width="100%">
        <tr>
            <td width="20%" style="border:none; text-align:center;">
                <img src="{{ public_path('images/logo_semahn2.png') }}">
            </td>
            <td width="80%" style="border:none; text-align:center;">
                <strong>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</strong><br>
                UNIDAD DE APOYO ADMINISTRATIVO – ÁREA DE INFORMÁTICA<br>
                <em>"2025, Año de Rosario Castellanos Figueroa"</em>
            </td>
        </tr>
    </table>
</div>

<div class="titulo">Formato B – Equipos de Cómputo / Impresoras</div>

{{-- ================= DATOS GENERALES ================= --}}
<div class="section">Datos del Servicio</div>
<table>
    <tr>
        <th>Folio</th>
        <td>{{ $servicio->folio }}</td>

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

{{-- ================= TIPO EQUIPO ================= --}}
<div class="section">Tipo de Equipo</div>
<div class="texto">{{ $servicio->subtipo }}</div>

{{-- ================= DESCRIPCIÓN ================= --}}
<div class="section">Descripción del Servicio</div>
<div class="texto">{{ $servicio->descripcion_servicio }}</div>

{{-- ================= DETALLES EQUIPO ================= --}}
<div class="section">Detalles del Equipo</div>
<table>
    <tr>
        <th>Equipo</th><td>{{ $servicio->equipo }}</td>
        <th>Marca</th><td>{{ $servicio->marca }}</td>
    </tr>
    <tr>
        <th>Modelo</th><td>{{ $servicio->modelo }}</td>
        <th>No. Serie</th><td>{{ $servicio->numero_serie }}</td>
    </tr>
</table>

{{-- ================= DIAGNÓSTICO ================= --}}
<div class="section">Diagnóstico</div>
<div class="texto">{{ $servicio->diagnostico }}</div>

{{-- ================= TRABAJO REALIZADO ================= --}}
<div class="section">Trabajo Realizado</div>
<div class="texto">{{ $servicio->trabajo_realizado }}</div>

{{-- ================= CONCLUSIÓN ================= --}}
<div class="section">Conclusión</div>
<div class="texto">{{ $servicio->conclusion_servicio }}</div>

{{-- ================= MATERIALES ================= --}}
<div class="section">Materiales Utilizados</div>
<table>
    <thead>
        <tr>
            <th>Material</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materiales as $m)
            <tr>
                <td>{{ $m->nombre }}</td>
                <td>{{ $m->cantidad }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="text-center">Sin materiales</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- ================= OBSERVACIONES ================= --}}
<div class="section">Observaciones</div>
<div class="texto">{{ $servicio->observaciones }}</div>

{{-- ================= FIRMAS ================= --}}
<div class="section">Firmas</div>
<table class="firmas" width="100%">
    <tr>
        <td width="33%">
            <strong>Usuario</strong><br><br>
            {{ $servicio->firma_usuario ?? '___________________' }}
        </td>
        <td width="33%">
            <strong>Técnico</strong><br><br>
            {{ $servicio->firma_tecnico ?? '___________________' }}
        </td>
        <td width="33%">
            <strong>Jefe de Área</strong><br><br>
            {{ $servicio->firma_jefe_area ?? '___________________' }}
        </td>
    </tr>
</table>

<div class="footer">
    Sistema de Formatos Digitales – SEMAHN
</div>

</body>
</html>
