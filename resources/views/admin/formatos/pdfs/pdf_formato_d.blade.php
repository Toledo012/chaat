<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Formato D PDF</title>
<style>
body{font-family:Arial,sans-serif;font-size:13px;margin:25px;}
.header{text-align:center;border-bottom:2px solid #000;padding-bottom:10px;margin-bottom:20px;}
.header img{width:100px;float:left;}
.section-title{font-weight:bold;background:#e2e3e5;padding:4px;margin-top:10px;}
table{width:100%;border-collapse:collapse;}
th,td{border:1px solid #000;padding:6px;text-align:left;}
.firmas td{border:none;text-align:center;padding-top:35px;}
</style>
</head>
<body>

<div class="header">
  <img src="{{ public_path('images/logo_semahn2.png') }}">
  <h3>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h3>
  <p>UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA</p>
</div>

<p><strong>Formato D - Mantenimiento de Equipos Personales</strong></p>
<p><strong>Folio:</strong> {{ $servicio->folio }} | <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</p>

<div class="section-title">Datos del Equipo</div>
<table>
  <tr><th>Equipo</th><td>{{ $servicio->equipo }}</td>
      <th>Marca</th><td>{{ $servicio->marca }}</td></tr>
  <tr><th>Modelo</th><td>{{ $servicio->modelo }}</td>
      <th>Serie</th><td>{{ $servicio->serie }}</td></tr>
</table>

<div class="section-title">Personal Involucrado</div>
<table>
  <tr><th>Otorgante</th><td>{{ $servicio->otorgante }}</td>
      <th>Receptor</th><td>{{ $servicio->receptor }}</td></tr>
</table>

<div class="section-title">Observaciones</div>
<p>{{ $servicio->observaciones }}</p>

<table class="firmas">
<tr>
  <td>_________________________<br><strong>Otorgante</strong><br>{{ $servicio->otorgante }}</td>
  <td>_________________________<br><strong>Receptor</strong><br>{{ $servicio->receptor }}</td>
  <td>_________________________<br><strong>Jefe de Área</strong><br>{{ $servicio->firma_jefe_area }}</td>
</tr>
</table>

</body>
</html>
    