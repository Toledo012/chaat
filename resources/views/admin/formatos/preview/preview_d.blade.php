<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Vista previa - Formato D</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{font-family:Arial,sans-serif;background:#f8f9fa;padding:2rem;}
.header{border-bottom:3px solid #198754;margin-bottom:20px;padding-bottom:10px;}
.header img{width:100px;}
.section-title{background:#d1e7dd;color:#0a3622;font-weight:bold;padding:5px;border-radius:4px;margin-top:1rem;}
.firmas td{border:none;text-align:center;padding-top:30px;}
</style>
</head>
<body>
<div class="container bg-white shadow p-4 rounded">

<div class="row align-items-center header">
  <div class="col-3 text-center"><img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo"></div>
  <div class="col-9 text-center">
    <h5>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h5>
    <p class="mb-0">UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA</p>
    <small><em>"2025, Año de Rosario Castellanos Figueroa"</em></small>
  </div>
</div>

<h5 class="text-success fw-bold text-center">Formato D - Mantenimiento de Equipos Personales</h5>

<div class="section-title">Datos del Equipo</div>
<table class="table table-bordered">
  <tr><th>Folio</th><td>{{ $servicio->folio ?? 'N/A' }}</td>
      <th>Fecha</th><td>{{ \Carbon\Carbon::parse($servicio->fecha ?? now())->format('d/m/Y') }}</td></tr>
  <tr><th>Equipo</th><td>{{ $servicio->equipo }}</td>
      <th>Marca</th><td>{{ $servicio->marca }}</td></tr>
  <tr><th>Modelo</th><td>{{ $servicio->modelo }}</td>
      <th>Serie</th><td>{{ $servicio->serie }}</td></tr>
</table>

<div class="section-title">Personal Involucrado</div>
<table class="table table-bordered">
  <tr><th>Otorgante</th><td>{{ $servicio->otorgante }}</td>
      <th>Receptor</th><td>{{ $servicio->receptor }}</td></tr>
</table>

<div class="section-title">Observaciones</div>
<p>{{ $servicio->observaciones ?? 'Ninguna.' }}</p>

<div class="section-title">Firmas</div>
<table class="firmas" width="100%">
<tr>
  <td><strong>Otorgante</strong><br>{{ $servicio->otorgante ?? '___________________' }}</td>
  <td><strong>Receptor</strong><br>{{ $servicio->receptor ?? '___________________' }}</td>
  <td><strong>Jefe de Área</strong><br>{{ $servicio->firma_jefe_area ?? '___________________' }}</td>
</tr>
</table>

</div>
</body>
</html>
