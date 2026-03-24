<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato C — {{ $servicio->folio }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; line-height: 1.4; }
        .header { border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
        .header img { width: 90px; float: left; }
        .header .text { text-align: center; }
        .titulo { text-align:center; font-weight:bold; font-size:14px; margin:10px 0 5px; text-transform:uppercase; }
        .subtitulo { text-align:center; font-size:11px; margin-bottom:10px; }
        .section-title { font-weight:bold; background:#e2e3e5; padding:4px; margin-top:8px; font-size:11px; }
        table { width:100%; border-collapse:collapse; margin-top:4px; }
        th, td { border:1px solid #000; padding:5px; vertical-align:top; }
        th { background-color:#f2f2f2; width:20%; }
        .firmas td { border:none; text-align:center; padding-top:30px; }
        .footer { text-align:center; font-size:10px; margin-top:12px; border-top:1px solid #000; padding-top:5px; }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('images/logo_semahn2.png') }}">
    <div class="text">
        <strong>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</strong><br>
        UNIDAD DE APOYO ADMINISTRATIVO — ÁREA DE INFORMÁTICA<br>
        <em>"2025, Año de Rosario Castellanos Figueroa"</em>
    </div>
    <div style="clear:both"></div>
</div>

<div class="titulo">Formato C — Redes / Telefonía</div>
<div class="subtitulo">Atención de servicios de redes o telefonía institucional</div>

<div class="section-title">Datos del Servicio</div>
<table>
    <tr>
        <th width="20%">Folio</th><td>{{ $servicio->folio }}</td>
        <th width="15%">Fecha</th><td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <th>Departamento</th>
        <td colspan="3">{{ $servicio->departamento_nombre ?? 'No especificado' }}</td>
    </tr>
    <tr>
        <th>Tipo de Atención</th>
        <td colspan="3">
            {{ $servicio->tipo_atencion ?? '—' }}
            @if($servicio->tipo_atencion === 'Memo' && $servicio->num_memo)
                — Memo N° {{ $servicio->num_memo }}
            @endif
        </td>
    </tr>
</table>

<div class="section-title">Datos Técnicos</div>
<table>
    <tr>
        <th>Tipo de Red</th><td>{{ $servicio->tipo_red }}</td>
        <th>Tipo de Servicio</th><td>{{ $servicio->tipo_servicio }}</td>
    </tr>
    <tr>
        <th>Origen de Falla</th><td colspan="3">{{ $servicio->origen_falla ?? '—' }}</td>
    </tr>
</table>

<div class="section-title">Descripción del Servicio</div>
<p>{{ $servicio->descripcion_servicio ?? 'Sin descripción.' }}</p>

<div class="section-title">Diagnóstico</div>
<p>{{ $servicio->diagnostico ?? 'No especificado.' }}</p>

<div class="section-title">Trabajo Realizado</div>
<p>{{ $servicio->trabajo_realizado ?? 'No especificado.' }}</p>

<div class="section-title">Materiales Utilizados</div>
<table>
    <thead><tr><th>Material</th><th width="20%">Cantidad</th></tr></thead>
    <tbody>
    @forelse($materiales as $m)
        <tr><td>{{ $m->nombre }}</td><td style="text-align:center">{{ $m->cantidad }}</td></tr>
    @empty
        <tr><td colspan="2" style="text-align:center">Sin materiales</td></tr>
    @endforelse
    </tbody>
</table>

<div class="section-title">Observaciones</div>
<p>{{ $servicio->observaciones ?? 'Ninguna.' }}</p>

<table class="firmas" width="100%">
    <tr>
        <td>___________________________<br><strong>Usuario</strong><br>{{ $servicio->firma_usuario ?? '' }}</td>
        <td>___________________________<br><strong>Técnico</strong><br>{{ $servicio->firma_tecnico ?? '' }}</td>
        <td>___________________________<br><strong>Jefe de Área</strong><br>{{ $servicio->firma_jefe_area ?? '' }}</td>
    </tr>
</table>

<div class="footer">Generado desde el Sistema de Formatos Digitales — SEMAHN</div>
</body>
</html>
