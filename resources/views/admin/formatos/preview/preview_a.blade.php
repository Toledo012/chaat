<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista previa - Formato A</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: "Arial", sans-serif;
            font-size: 14px;
            background-color: #f8f9fa;
            padding: 2rem;
        }
        .header {
            border-bottom: 3px solid #157347;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            width: 110px;
        }
        .titulo {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            color: #0a3622;
        }
        .subtitulo {
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .section-title {
            background-color: #d1e7dd;
            color: #0a3622;
            font-weight: bold;
            padding: 6px;
            border-radius: 4px;
            margin-top: 1rem;
            margin-bottom: .5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: top;
        }
        .firmas td {
            border: none;
            text-align: center;
            padding-top: 30px;
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #6c757d;
            border-top: 2px solid #ccc;
            margin-top: 2rem;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<div class="container bg-white shadow p-4 rounded">
    {{-- ENCABEZADO INSTITUCIONAL --}}
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

    {{-- TÍTULO --}}
    <h5 class="titulo">Formato A - Soporte y Desarrollo</h5>
    <p class="subtitulo">Atención de servicios de soporte técnico o desarrollo institucional</p>

    {{-- DATOS DEL SERVICIO --}}
    <div class="section-title">Datos del Servicio</div>
    <table class="table table-bordered">
        <tr>
            <th width="25%">Folio</th>
            <td>{{ $servicio->folio ?? 'N/A' }}</td>
            <th width="25%">Fecha</th>
            <td>{{ \Carbon\Carbon::parse($servicio->fecha ?? now())->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Tipo de Formato</th>
            <td colspan="3">{{ $servicio->tipo_formato ?? 'A' }}</td>
        </tr>
    </table>

    {{-- PETICIÓN --}}
    <div class="section-title">Petición del Servicio</div>
    <p>{{ $servicio->peticion ?? 'Sin descripción registrada.' }}</p>

    {{-- TRABAJO REALIZADO --}}
    <div class="section-title">Trabajo Realizado</div>
    <p>{{ $servicio->trabajo_realizado ?? 'No se especifica.' }}</p>

    {{-- CONCLUSIÓN --}}
    <div class="section-title">Conclusión del Servicio</div>
    <p>{{ $servicio->conclusion_servicio ?? 'Sin datos.' }}</p>

    {{-- OBSERVACIONES --}}
    <div class="section-title">Observaciones</div>
    <p>{{ $servicio->observaciones ?? 'Ninguna.' }}</p>

    {{-- FIRMAS --}}
    <div class="section-title">Firmas de Conformidad</div>
    <table class="firmas" width="100%">
        <tr>
            <td>
                <strong>Usuario Solicitante</strong><br>
                {{ $servicio->firma_usuario ?? '___________________' }}
            </td>
            <td>
                <strong>Realiza el Servicio</strong><br>
                {{ $servicio->firma_tecnico ?? '___________________' }}
            </td>
            <td>
                <strong>Jefe de Área</strong><br>
                {{ $servicio->firma_jefe_area ?? '___________________' }}
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>Secretaría de Medio Ambiente e Historia Natural - Unidad de Apoyo Administrativo</p>
        <p>Generado desde el Sistema de Formatos Digitales</p>
    </div>
</div>

</body>
</html>
        