<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; margin: 20px; color: #000; }
        .portada { text-align: center; padding-top: 50px; page-break-after: always; }
        .portada img { width: 120px; margin-bottom: 20px; }

        .info-filtros { text-align: left; margin: 20px auto; border: 1px solid #000; padding: 10px; width: 90%; background: #f9f9f9; }

        .grafico-barras { width: 90%; margin: 20px auto; text-align: left; }
        .barra-item { margin-bottom: 8px; }
        .barra-bg { background: #eee; height: 16px; border-radius: 3px; border: 1px solid #ccc; width: 70%; display: inline-block; vertical-align: middle; }
        .barra-fill { height: 100%; color: white; font-size: 8px; text-align: right; padding-right: 5px; line-height: 16px; }
        .color-a { background-color: #0d6efd; } .color-b { background-color: #198754; }
        .color-c { background-color: #ffc107; color: #000; } .color-d { background-color: #dc3545; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; vertical-align: middle; }
        th { background-color: #e2e3e5; text-transform: uppercase; font-size: 8px; }

        .header-seccion { text-align: center; border-bottom: 2px solid #399e91; padding-bottom: 5px; margin: 30px 0 15px 0; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; border-top: 1px solid #000; padding-top: 5px; }

        .tabla-detalle { margin-bottom: 15px; table-layout: fixed; }
        .tabla-detalle th { text-align: left; width: 20%; background: #f2f2f2; }
        .tabla-detalle td { text-align: left; width: 30%; }
        .tipo-text { font-weight: bold; color: #0d6efd; }

        /* ===== Detalle Técnico (jerarquía mejorada) ===== */
        .detalle-card { border: 1px solid #cfe0dd; margin: 10px 0 14px 0; page-break-inside: avoid; }
        .detalle-head { margin-top: 0; background-color: #399e91; }
        .detalle-head td { border: none; color: #ffffff; padding: 6px 10px; vertical-align: middle; }
        .detalle-head .folio { text-align: left; font-size: 11px; font-weight: bold; letter-spacing: 0.3px; }
        .detalle-head .tipo { text-align: right; }
        .detalle-head .tipo-badge { border: 1px solid #ffffff; padding: 1px 8px; font-size: 8px; font-weight: bold; }
        .detalle-meta { margin-top: 0; }
        .detalle-meta td { border: none; border-bottom: 1px solid #eeeeee; padding: 5px 10px; text-align: left; font-size: 9px; vertical-align: top; }
        .detalle-meta .lbl { color: #6c757d; text-transform: uppercase; font-size: 7px; font-weight: bold; }
        .bloque { padding: 7px 10px; }
        .bloque .titulo { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
        .bloque .texto { font-size: 9px; color: #212529; line-height: 1.4; text-align: justify; }
        .bloque-desc { border-left: 3px solid #399e91; background-color: #f4faf9; }
        .bloque-desc .titulo { color: #2c7a70; }
        .bloque-obs { border-left: 3px solid #c9a227; background-color: #fdfaf0; }
        .bloque-obs .titulo { color: #9c7a10; }
        .text-empty { color: #adb5bd; font-style: italic; }
    </style>
</head>
<body>

{{-- ====================== PORTADA ====================== --}}
<div class="portada">
    <img src="{{ public_path('images/logo_semahn2.png') }}" alt="Logo">
    <h1>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h1>
    <h3>REPORTE ESTRATÉGICO DE FORMATOS DIGITALES</h3>

    <div class="info-filtros">
        <strong>Filtros aplicados:</strong><br>
        Rango: {{ $fecha_inicio ?? 'Inicio' }} al {{ $fecha_fin ?? 'Actual' }} |
        Tipo: {{ $tipo ?? 'Todos' }} | Usuario: {{ $usuario ?? 'Todos' }}
    </div>

    <div class="grafico-barras">
        <p><strong>Distribución de Servicios por Tipo</strong></p>
        @foreach(['A','B','C','D'] as $t)
            @php
                $cant = $statsTipos[$t];
                $pct = $totalGlobal > 0 ? round(($cant/$totalGlobal)*100) : 0;
            @endphp
            <div class="barra-item">
                <div style="display:inline-block; width: 80px;">Tipo {{ $t }} ({{ $cant }})</div>
                <div class="barra-bg">
                    <div class="barra-fill color-{{ strtolower($t) }}" style="width: {{ $pct }}%;">{{ $pct }}%</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- GRÁFICA DE PASTEL (CORREGIDA) --}}
    <p><strong>Participación por Equipo Técnico</strong></p>
    @php
        $perimetro = 100.53;
        $acumulado = 0;
        $colores = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6610f2', '#fd7e14'];
    @endphp
    <svg viewBox="0 0 32 32" width="120" height="120" style="margin: auto; display: block;">
        @foreach($resumenUsuarios as $u)
            @php
                $valor_segmento = ($u['total'] / $totalGlobal) * $perimetro;
            @endphp
            <circle r="16" cx="16" cy="16" fill="transparent" stroke="{{ $colores[$loop->index % 6] }}"
                    stroke-width="32" stroke-dasharray="{{ $valor_segmento }} {{ $perimetro }}" stroke-dashoffset="-{{ $acumulado }}"
                    transform="rotate(-90 16 16)" />
            @php $acumulado += $valor_segmento; @endphp
        @endforeach
    </svg>
    <div style="margin-top: 10px;">
        @foreach($resumenUsuarios as $nombre => $u)
            <span style="font-size: 8px; margin-right: 8px;">
                <span style="color: {{ $colores[$loop->index % 6] }};">●</span> {{ $nombre }} ({{ round(($u['total']/$totalGlobal)*100) }}%)
            </span>
        @endforeach
    </div>
</div>

{{-- ====================== TABLA DE USUARIOS ====================== --}}
<div class="header-seccion">
    <h3>Resumen de Actividad por Usuario</h3>
</div>
<table>
    <thead>
    <tr>
        <th style="text-align: left;">Nombre del Usuario</th>
        <th>Total Formatos</th>
        <th>Tipo A</th>
        <th>Tipo B</th>
        <th>Tipo C</th>
        <th>Tipo D</th>
        <th>% Part.</th>
    </tr>
    </thead>
    <tbody>
    @foreach($resumenUsuarios as $nombre => $u)
        <tr>
            <td style="text-align: left;">{{ $nombre ?: 'N/A' }}</td>
            <td><strong>{{ $u['total'] }}</strong></td>
            <td>{{ $u['A'] }}</td>
            <td>{{ $u['B'] }}</td>
            <td>{{ $u['C'] }}</td>
            <td>{{ $u['D'] }}</td>
            <td>{{ round(($u['total'] / $totalGlobal) * 100) }}%</td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- ====================== TABLA MENSUAL ) ====================== --}}
<div class="header-seccion">
    <h3>Comparativa Mensual</h3>
</div>
<table style="width: 50%; margin: 10px auto;">
    <thead>
    <tr>
        <th>Mes / Año</th>
        <th>Cantidad de Formatos</th>
    </tr>
    </thead>
    <tbody>
    @foreach($analisisMensual as $mes => $cantidad)
        <tr>
            <td>{{ \Carbon\Carbon::parse($mes.'-01')->translatedFormat('F Y') }}</td>
            <td><strong>{{ $cantidad }}</strong></td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- ====================== DETALLE TÉCNICO ====================== --}}
<div class="header-seccion" style="page-break-before: always;">
    <h3>Detalle Técnico de Registros</h3>
</div>

@foreach($formatos as $f)
    @php
        // Cada formato nombra distinto su campo principal (ver plantillas
        // pdf_formato_*). Mapeamos la "descripción" según el tipo:
        //   A      -> peticion (Petición del Servicio); fallback: trabajo_realizado
        //   B / C  -> descripcion_servicio
        //   R      -> descripcion (Recepción)
        //   D      -> no tiene descripción (solo observaciones)
        $tf = strtoupper($f->tipo_formato ?? '');
        $descripcion = match ($tf) {
            'A'      => $f->peticion ?? $f->trabajo_realizado ?? null,
            'B', 'C' => $f->descripcion_servicio ?? null,
            'R'      => $f->descripcion ?? null,
            default  => null,
        };
        $observacion = $f->observaciones ?? null;
    @endphp
    <div class="detalle-card">
        <table class="detalle-head">
            <tr>
                <td class="folio">{{ $f->folio ?? 'ID-'.$f->id_servicio }}</td>
                <td class="tipo"><span class="tipo-badge">FORMATO {{ strtoupper($f->tipo_formato) }}</span></td>
            </tr>
        </table>

        <table class="detalle-meta">
            <tr>
                <td width="50%"><span class="lbl">Usuario técnico</span><br>{{ $f->usuarios_formatos ?: 'N/A' }}</td>
                <td width="50%"><span class="lbl">Fecha</span><br>{{ \Carbon\Carbon::parse($f->fecha)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <span class="lbl">Información</span><br>
                    @if($f->tipo_formato == 'A')
                        <strong>Solicitante:</strong> {{ $f->firma_usuario ?? 'N/A' }} &nbsp;|&nbsp; <strong>Área:</strong> {{ $f->departamento_nombre ?? 'N/A' }}
                    @elseif($f->tipo_formato == 'B')
                        <strong>Equipo:</strong> {{ $f->equipo ?? 'N/A' }} &nbsp;|&nbsp; <strong>Marca:</strong> {{ $f->marca ?? 'N/A' }} &nbsp;|&nbsp; <strong>Serie:</strong> {{ $f->numero_serie ?? 'N/A' }}
                    @elseif($f->tipo_formato == 'C')
                        <strong>Nodo:</strong> {{ $f->numero_nodo ?? 'N/A' }} &nbsp;|&nbsp; <strong>Ubicación:</strong> {{ $f->ubicacion ?? 'N/A' }}
                    @else
                        <strong>Área:</strong> {{ $f->departamento_nombre ?? 'N/A' }}
                    @endif
                </td>
            </tr>
        </table>

        <div class="bloque bloque-desc">
            <div class="titulo">Descripción del servicio</div>
            <div class="texto {{ $descripcion ? '' : 'text-empty' }}">{{ $descripcion ?: 'Sin descripción registrada.' }}</div>
        </div>

        <div class="bloque bloque-obs">
            <div class="titulo">Observaciones</div>
            <div class="texto {{ $observacion ? '' : 'text-empty' }}">{{ $observacion ?: 'Sin observaciones registradas.' }}</div>
        </div>
    </div>
@endforeach

<div class="footer">
    Sistema de Formatos Digitales SEMAHN 2026 - Generado el {{ date('d/m/Y H:i') }}
</div>

</body>
</html>
