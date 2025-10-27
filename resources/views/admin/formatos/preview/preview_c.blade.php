<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Vista previa - Formato C</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{font-family:Arial,sans-serif;background:#f8f9fa;padding:2rem;}
.header{border-bottom:3px solid #0dcaf0;margin-bottom:20px;padding-bottom:10px;}
.header img{width:100px;}
.section-title{background:#cff4fc;color:#055160;font-weight:bold;padding:5px;border-radius:4px;margin-top:1rem;}
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

<h5 class="text-info fw-bold text-center">Formato C - Redes y Telefonía</h5>

<div class="section-title">Datos del Servicio</div>
<table class="table table-bordered">
  <tr><th>Folio</th><td>{{ $servicio->folio ?? 'N/A' }}</td>
      <th>Fecha</th><td>{{ \Carbon\Carbon::parse($servicio->fecha ?? now())->format('d/m/Y') }}</td></tr>
  <tr><th>Tipo de Red</th><td>{{ $servicio->tipo_red ?? '-' }}</td>
      <th>Tipo de Servicio</th><td>{{ $servicio->tipo_servicio ?? '-' }}</td></tr>
</table>

<div class="section-title">Descripción del Servicio</div>
<p>{{ $servicio->descripcion_servicio ?? 'Sin descripción.' }}</p>

<div class="section-title">Diagnóstico</div>
<p>{{ $servicio->diagnostico ?? 'No se especifica.' }}</p>

<div class="section-title">Origen de la Falla</div>
<p>{{ $servicio->origen_falla ?? 'Sin definir.' }}</p>

<div class="section-title">Trabajo Realizado</div>
<p>{{ $servicio->trabajo_realizado ?? 'N/A' }}</p>

<div class="section-title">Detalle del Servicio</div>
<p>{{ $servicio->detalle_realizado ?? 'Sin detalle.' }}</p>

<div class="section-title">Materiales Utilizados</div>
@if($materiales->isEmpty())
  <p>No se registraron materiales.</p>
@else
  <table class="table table-bordered">
    <thead><tr><th>Material</th><th>Cantidad</th></tr></thead>
    <tbody>
      @foreach($materiales as $mat)
        <tr><td>{{ $mat->nombre }}</td><td>{{ $mat->cantidad }}</td></tr>
      @endforeach
    </tbody>
  </table>
@endif

<div class="section-title">Observaciones</div>
<p>{{ $servicio->observaciones ?? 'Ninguna.' }}</p>

<div class="section-title">Firmas</div>
<table class="firmas" width="100%">
<tr>
  <td><strong>Usuario Solicitante</strong><br>{{ $servicio->firma_usuario ?? '___________________' }}</td>
  <td><strong>Realiza el Servicio</strong><br>{{ $servicio->firma_tecnico ?? '___________________' }}</td>
  <td><strong>Jefe de Área</strong><br>{{ $servicio->firma_jefe_area ?? '___________________' }}</td>
</tr>
</table>

</div>
</body>
</html>
