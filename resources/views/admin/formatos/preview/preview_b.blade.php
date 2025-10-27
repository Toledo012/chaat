<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Vista previa - Formato B</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{font-family:Arial,sans-serif;font-size:14px;background:#f8f9fa;padding:2rem;}
    .header{border-bottom:3px solid #157347;margin-bottom:20px;padding-bottom:10px;}
    .header img{width:110px;}
    .titulo{text-align:center;font-weight:bold;color:#0a3622;}
    .section-title{background:#d1e7dd;color:#0a3622;font-weight:bold;padding:6px;border-radius:4px;margin-top:1rem;}
    table{width:100%;border-collapse:collapse;}
    td,th{border:1px solid #dee2e6;padding:8px;vertical-align:top;}
    .firmas td{border:none;text-align:center;padding-top:30px;}
  </style>
</head>
<body>

<div class="container bg-white shadow p-4 rounded">
  <div class="row align-items-center header">
    <div class="col-3 text-center">
      <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo SEMAHN">
    </div>
    <div class="col-9 text-center">
      <h5>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h5>
      <p class="mb-0">UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA</p>
      <small><em>"2025, Año de Rosario Castellanos Figueroa"</em></small>
    </div>
  </div>

  <h5 class="titulo">Formato B - Equipos e Impresoras</h5>

  <div class="section-title">Datos Generales</div>
  <table class="table table-bordered">
    <tr><th>Folio</th><td>{{ $servicio->folio ?? 'N/A' }}</td>
        <th>Fecha</th><td>{{ \Carbon\Carbon::parse($servicio->fecha ?? now())->format('d/m/Y') }}</td></tr>
    <tr><th>Subtipo</th><td colspan="3">{{ $servicio->subtipo ?? '-' }}</td></tr>
  </table>

  <div class="section-title">Descripción del Servicio</div>
  <p>{{ $servicio->descripcion_servicio ?? 'Sin descripción.' }}</p>

  @if($servicio->subtipo === 'Computadora')
    <div class="section-title">Datos de la Computadora</div>
    <table class="table table-bordered">
      <tr><th>Marca</th><td>{{ $servicio->marca }}</td>
          <th>Modelo/Año</th><td>{{ $servicio->modelo }}</td></tr>
      <tr><th>Procesador</th><td>{{ $servicio->procesador }}</td>
          <th>RAM</th><td>{{ $servicio->ram }}</td></tr>
      <tr><th>Disco Duro</th><td>{{ $servicio->disco_duro }}</td>
          <th>S.O.</th><td>{{ $servicio->sistema_operativo }}</td></tr>
      <tr><th>N° Serie</th><td>{{ $servicio->numero_serie }}</td>
          <th>N° Inventario</th><td>{{ $servicio->numero_inventario }}</td></tr>
    </table>
  @elseif($servicio->subtipo === 'Impresora')
    <div class="section-title">Datos de la Impresora</div>
    <table class="table table-bordered">
      <tr><th>Marca</th><td>{{ $servicio->marca }}</td>
          <th>Modelo</th><td>{{ $servicio->modelo }}</td></tr>
      <tr><th>Diagnóstico</th><td colspan="3">{{ $servicio->diagnostico }}</td></tr>
    </table>
  @endif

  <div class="section-title">Diagnóstico y Trabajo Realizado</div>
  <p><strong>Diagnóstico: </strong>{{ $servicio->diagnostico ?? '-' }}</p>
  <p><strong>Origen de falla: </strong>{{ $servicio->origen_falla ?? '-' }}</p>
  <p><strong>Trabajo Realizado: </strong>{{ $servicio->trabajo_realizado ?? '-' }}</p>
  <p><strong>Conclusión: </strong>{{ $servicio->conclusion_servicio ?? '-' }}</p>

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

  <div class="section-title">Firmas de Conformidad</div>
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
