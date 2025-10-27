

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato A - PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #000; margin: 25px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header img { width: 100px; float: left; }
        .titulo { font-weight: bold; font-size: 16px; text-transform: uppercase; margin-bottom: 5px; }
        .subtitulo { font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        .section-title { font-weight: bold; background: #e2e3e5; padding: 4px; margin-top: 10px; }
        .firmas td { border: none; text-align: center; padding-top: 35px; }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('images/logo_semahn2.png') }}" alt="Logo SEMAHN">
    <h3>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h3>
    <p>UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA</p>
    <small><em>"2025, Año de Rosario Castellanos Figueroa"</em></small>
</div>

<p class="titulo">Formato A - Soporte y Desarrollo</p>
<p class="subtitulo">Atención de servicios de soporte técnico o desarrollo institucional</p>

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

<div class="section-title">Conclusión del Servicio</div>
<p>{{ $servicio->conclusion_servicio ?? 'Sin datos.' }}</p>

<div class="section-title">Observaciones</div>
<p>{{ $servicio->observaciones ?? 'Ninguna.' }}</p>

<table class="firmas">
    <tr>
        <td>
            ___________________________<br>
            <strong>Usuario Solicitante</strong><br>
            {{ $servicio->firma_usuario ?? '' }}
        </td>
        <td>
            ___________________________<br>
            <strong>Realiza el Servicio</strong><br>
            {{ $servicio->firma_tecnico ?? '' }}
        </td>
        <td>
            ___________________________<br>
            <strong>Jefe de Área</strong><br>
            {{ $servicio->firma_jefe_area ?? '' }}
        </td>
    </tr>
</table>

</body>
</html>
