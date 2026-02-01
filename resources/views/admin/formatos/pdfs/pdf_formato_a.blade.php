<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Formato A - PDF</title>

<style>
    @page {
        margin: 12px;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 10px;
        color: #000;
    }

    .formato {
        height: 48%;
        box-sizing: border-box;
    }

    .header {
        text-align: center;
        border-bottom: 1px solid #000;
        padding-bottom: 4px;
        margin-bottom: 4px;
        position: relative;
    }

    .header img {
        width: 50px;
        position: absolute;
        left: 0;
        top: 0;
    }

    .titulo {
        font-weight: bold;
        font-size: 12px;
        text-transform: uppercase;
        margin: 2px 0;
    }

    .subtitulo {
        font-size: 10px;
        margin-bottom: 4px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 3px;
    }

    th, td {
        border: 1px solid #000;
        padding: 3px;
        vertical-align: top;
    }

    th {
        width: 22%;
        background: #f1f1f1;
    }

    .section-title {
        font-weight: bold;
        background: #e2e3e5;
        padding: 2px;
        margin-top: 4px;
        margin-bottom: 2px;
    }

    .bloque {
        border: 1px solid #000;
        padding: 3px;
        min-height: 24px;
    }

    .firmas td {
        border: none;
        text-align: center;
        padding-top: 14px;
        font-size: 9px;
    }

    .corte {
        border-top: 1px dashed #000;
        margin: 6px 0;
        text-align: center;
        font-size: 8px;
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
            <td colspan="3">{{ $servicio->tipo_servicio ?? '—' }}</
        </tr>
    </table>

    
    




    <div class="section-title">Petición del Servicio</div>
    <div class="bloque">{{ $servicio->peticion ?? '—' }}</div>

    <div 

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
                Usuario Solicitante<br>
                {{ $servicio->firma_usuario }}
            </td>
            <td>
                _________________________<br>
                Técnico<br>
                {{ $servicio->firma_tecnico }}
            </td>
            <td>
                _________________________<br>
                Jefe de Área<br>
                {{ $servicio->firma_jefe_area }}
            </td>
        </tr>
    </table>
</div>



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

    <table>
        <tr>
            <th>Número de Formato</th>
            <td>{{ $servicio->id_servicio }}</td>
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
            <td colspan="3">{{ $servicio->tipo_servicio ?? '—' }}</
        </tr>
    </table>

    
    




    <div class="section-title">Petición del Servicio</div>
    <div class="bloque">{{ $servicio->peticion ?? '—' }}</div>

    <div 

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
                Usuario Solicitante<br>
                {{ $servicio->firma_usuario }}
            </td>
            <td>
                _________________________<br>
                Técnico<br>
                {{ $servicio->firma_tecnico }}
            </td>
            <td>
                _________________________<br>
                Jefe de Área<br>
                {{ $servicio->firma_jefe_area }}
            </td>
        </tr>
    </table>
</div>


</body>
</html>
