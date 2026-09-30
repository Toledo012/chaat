<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato D — {{ $servicio->folio }}</title>
    <style>
        @page { margin: 2cm; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
        }

        .formato { width:100%; box-sizing:border-box; }

        /* ── Encabezado ── */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
            position: relative;
        }
        .header img {
            width: 60px;
            position: absolute;
            left: 0;
            top: 0;
        }

        /* ── Títulos ── */
        .titulo   { font-weight:bold; font-size:13px; text-transform:uppercase; margin:5px 0; text-align:center; }
        .subtitulo{ font-size:10px; margin-bottom:8px; text-align:center; color:#555; }

        /* ── Tablas ── */
        table { width:100%; border-collapse:collapse; margin-top:4px; }
        th, td { border:1px solid #000; padding:5px; vertical-align:top; }
        th { width:20%; background:#f1f1f1; text-align:left; }

        /* ── Secciones ── */
        .section-title {
            font-weight:bold;
            background:#e2e3e5;
            padding:4px;
            margin-top:10px;
            margin-bottom:2px;
            border:1px solid #000;
        }

        /* ── Bloque de texto ── */
        .bloque { border:1px solid #000; padding:8px; min-height:35px; margin-bottom:4px; }
        .texto  { text-align:justify; margin:8px 0; }

        /* ── Firmas ── */
        .firmas { margin-top:30px; }
        .firmas td { border:none; text-align:center; padding-top:20px; font-size:10px; width:33%; }
    </style>
</head>
<body>

<div class="formato">

    {{-- Encabezado --}}
    <div class="header">
        <img src="{{ public_path('images/logo_semahn2.png') }}" alt="SEMAHN">
        <strong>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</strong><br>
        UNIDAD DE APOYO ADMINISTRATIVO — ÁREA DE INFORMÁTICA<br>
        <x-leyenda-anual :fecha="$servicio->fecha" />
    </div>

    <div class="titulo">Formato D — Mantenimiento Equipos Personales</div>
    <div class="subtitulo">Entrega y recepción de equipo personal para mantenimiento</div>

    {{-- Datos del servicio --}}
    <div class="section-title">Datos del Servicio</div>
    <table>
        <tr>
            <th>Folio / ID</th>
            <td>{{ $servicio->folio ?? $servicio->id_servicio }}</td>
            <th>Fecha</th>
            <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Departamento</th>
            <td>
                {{ $departamentos->firstWhere('id_departamento', $servicio->id_departamento)?->nombre ?? 'No asignado' }}
            </td>
            <th>Tipo de Atención</th>
            <td>
                {{ $servicio->tipo_atencion ?? '—' }}
                @if($servicio->tipo_atencion === 'Memo' && $servicio->num_memo)
                    — N° {{ $servicio->num_memo }}
                @endif
            </td>
        </tr>
    </table>

    <p class="texto">
        El C. <strong>{{ $servicio->otorgante ?? '—' }}</strong> entrega el equipo con las siguientes características:
    </p>

    {{-- Datos del equipo --}}
    <div class="section-title">Datos del Equipo</div>
    <table>
        <tr>
            <th>Equipo</th>
            <td>{{ $servicio->equipo ?? '—' }}</td>
            <th>Marca</th>
            <td>{{ $servicio->marca ?? '—' }}</td>
        </tr>
        <tr>
            <th>Modelo</th>
            <td>{{ $servicio->modelo ?? '—' }}</td>
            <th>No. Serie</th>
            <td>{{ $servicio->serie ?? '—' }}</td>
        </tr>
    </table>

    <p class="texto">
        Sirva el presente formato como comprobante de entrega del equipo mencionado, que pertenece al
        C. <strong>{{ $servicio->otorgante ?? '—' }}</strong>, al personal del Área de Informática de la
        Secretaría de Medio Ambiente e Historia Natural, que se compromete a realizar el servicio de
        manera cuidadosa y profesional.
    </p>

    <div class="section-title">Observaciones</div>
    <div class="bloque">{{ $servicio->observaciones ?: 'Ninguna.' }}</div>

    {{-- Firmas --}}
    <table class="firmas">
        <tr>
            <td>
                _________________________<br>
                <strong>Otorgante</strong><br>
                {{ $servicio->otorgante ?? '' }}
            </td>
            <td>
                _________________________<br>
                <strong>Receptor</strong><br>
                {{ $servicio->receptor ?? '' }}
            </td>
            <td>
                _________________________<br>
                <strong>Jefe de Área</strong><br>
                {{ $servicio->firma_jefe_area ?? '' }}
            </td>
        </tr>
    </table>

</div>
</body>
</html>
