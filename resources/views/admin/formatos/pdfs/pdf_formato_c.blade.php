<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Formato C PDF</title>
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

<p><strong>Formato C - Redes y Telefonía</strong></p>
<p><strong>Folio:</strong> {{ $servicio->folio }} | <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</p>

<div class="section-title">Datos del Servicio</div>
<p><strong>Tipo de Red:</strong> {{ $servicio->tipo_red }} <br>
<strong>Tipo de Servicio:</strong> {{ $servicio->tipo_servicio }}</p>

<div class="section-title">Diagnóstico</div>
<p>{{ $servicio->diagnostico }}</p>

<div class="section-title">Trabajo Realizado</div>
<p>{{ $servicio->trabajo_realizado }}</p>

<div class="section-title">Materiales Utilizados</div>
@if($materiales->isEmpty())
  <p>No se registraron materiales.</p>
@else
  <table>
    <thead><tr><th>Material</th><th>Cantidad</th></tr></thead>
    <tbody>
      @foreach($materiales as $m)
        <tr><td>{{ $m->nombre }}</td><td>{{ $m->cantidad }}</td></tr>
      @endforeach
    </tbody>
  </table>
@endif

<div class="section-title">Observaciones</div>
<p>{{ $servicio->observaciones }}</p>

<table class="firmas">
<tr>
  <td>_________________________<br><strong>Usuario Solicitante</strong><br>{{ $servicio->firma_usuario }}</td>
  <td>_________________________<br><strong>Realiza el Servicio</strong><br>{{ $servicio->firma_tecnico }}</td>
  <td>_________________________<br><strong>Jefe de Área</strong><br>{{ $servicio->firma_jefe_area }}</td>
</tr>
</table>

</body>
</html>
