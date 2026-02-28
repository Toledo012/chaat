<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Formatos - SEMAHN</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 25px; color: #000; }
        .portada { text-align: center; margin-top: 80px; }
        .portada img { width: 120px; margin-bottom: 20px; }
        .portada h1 { font-size: 20px; margin-bottom: 10px; }
        .portada h3 { font-size: 16px; margin-bottom: 5px; }
        .portada p { font-size: 13px; }

        .info-filtros { text-align: left; margin: 30px auto; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 10px; width: 85%; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
        th { background-color: #e2e3e5; }

        .estadisticas { width: 85%; margin: 20px auto; font-size: 13px; }

        .grafico { width: 85%; margin: 20px auto; }
        .barra { display: flex; align-items: center; margin-bottom: 8px; }
        .etiqueta { width: 150px; text-align: right; padding-right: 10px; font-weight: bold; font-size: 11px; }
        .barra-contenedor { flex: 1; background-color: #f1f1f1; height: 16px; border-radius: 3px; overflow: hidden; border: 1px solid #ccc; }
        .relleno { height: 100%; text-align: right; color: #fff; font-size: 10px; padding-right: 4px; line-height: 16px; }
        .a { background-color: #0d6efd; }
        .b { background-color: #198754; }
        .c { background-color: #ffc107; color:#000; }
        .d { background-color: #dc3545; }

        .leyenda { width: 80%; margin: 15px auto; text-align: center; font-size: 12px; }
        .cuadro { width: 12px; height: 12px; margin-right: 5px; display: inline-block; border: 1px solid #000; }
        .cuadro.a { background: #0d6efd; } .cuadro.b { background: #198754; } .cuadro.c { background: #ffc107; } .cuadro.d { background: #dc3545; }

        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; page-break-before: always; }
        .header img { width: 80px; float: left; }
        .tipo { font-weight: bold; color: #0a3622; text-align: center; }
        .separador { border-top: 1px solid #ccc; margin: 10px 0; }

        .footer { text-align: center; font-size: 10px; margin-top: 30px; border-top: 1px solid #000; padding-top: 5px; }

        .resumen { page-break-before: always; margin-top: 40px; text-align: justify; }
        .resumen h3 { text-align: center; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; }

        .tabla-mensual { width: 100%; margin: 20px auto; border-collapse: collapse; }
        .tabla-mensual th, .tabla-mensual td { text-align: center; }
        .tabla-mensual th { background: #f8f9fa; }

        svg { display: block; margin: 20px auto; }
    </style>
</head>
<body>

{{-- ====================== PORTADA ====================== --}}
<div class="portada">
    <img src="{{ public_path('images/logo_semahn2.png') }}" alt="Logo SEMAHN">
    <h1>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h1>
    <h3>UNIDAD DE APOYO ADMINISTRATIVO</h3>
    <p>ÁREA DE INFORMÁTICA</p>
    <h2 style="margin-top: 50px;">REPORTE GENERAL DE FORMATOS</h2>
    <p><strong>Fecha de emisión:</strong> {{ now()->format('d/m/Y H:i') }}</p>

    <div class="info-filtros">
        <p><strong>Filtros aplicados:</strong></p>
        <table style="border:none; width: 100%;">
            <tr style="border:none;">
                <td style="border:none;"><strong>Tipo:</strong> {{ $tipo ?? 'Todos' }}</td>
                <td style="border:none;"><strong>Usuario:</strong> {{ $usuario ?? 'Todos' }}</td>
                <td style="border:none;"><strong>Fecha:</strong> {{ $fecha ? \Carbon\Carbon::parse($fecha)->format('d/m/Y') : 'Todas' }}</td>
            </tr>
        </table>
    </div>

    @php
        $totalA = $formatos->where('tipo_formato', 'A')->count();
        $totalB = $formatos->where('tipo_formato', 'B')->count();
        $totalC = $formatos->where('tipo_formato', 'C')->count();
        $totalD = $formatos->where('tipo_formato', 'D')->count();
        $total = $formatos->count();
        $max = max(1, $total);
        function pct($v,$t){ return round(($v/$t)*100,1); }
    @endphp

    <table class="estadisticas">
        <tr><th>Tipo de Formato</th><th>Total</th><th>Porcentaje</th></tr>
        <tr><td>A - Soporte</td><td>{{ $totalA }}</td><td>{{ pct($totalA,$max) }}%</td></tr>
        <tr><td>B - Equipos</td><td>{{ $totalB }}</td><td>{{ pct($totalB,$max) }}%</td></tr>
        <tr><td>C - Redes</td><td>{{ $totalC }}</td><td>{{ pct($totalC,$max) }}%</td></tr>
        <tr><td>D - Mantenimiento Personal</td><td>{{ $totalD }}</td><td>{{ pct($totalD,$max) }}%</td></tr>
        <tr style="font-weight: bold; background: #eee;"><td>Total general</td><td>{{ $total }}</td><td>100%</td></tr>
    </table>

    {{-- PASTEL SVG --}}
    <svg viewBox="0 0 32 32" width="150" height="150">
        @php
            $sum = max(1, $totalA + $totalB + $totalC + $totalD);
            $a1 = ($totalA / $sum) * 100;
            $a2 = ($totalB / $sum) * 100;
            $a3 = ($totalC / $sum) * 100;
            $a4 = 100 - ($a1 + $a2 + $a3);
        @endphp
        <circle r="16" cx="16" cy="16" fill="transparent" stroke="#0d6efd" stroke-width="32" stroke-dasharray="{{ $a1 }} 100" transform="rotate(-90 16 16)" />
        <circle r="16" cx="16" cy="16" fill="transparent" stroke="#198754" stroke-width="32" stroke-dasharray="{{ $a2 }} 100" stroke-dashoffset="-{{ $a1 }}" transform="rotate(-90 16 16)" />
        <circle r="16" cx="16" cy="16" fill="transparent" stroke="#ffc107" stroke-width="32" stroke-dasharray="{{ $a3 }} 100" stroke-dashoffset="-{{ $a1 + $a2 }}" transform="rotate(-90 16 16)" />
        <circle r="16" cx="16" cy="16" fill="transparent" stroke="#dc3545" stroke-width="32" stroke-dasharray="{{ $a4 }} 100" stroke-dashoffset="-{{ $a1 + $a2 + $a3 }}" transform="rotate(-90 16 16)" />
    </svg>

    <div class="leyenda">
        <span class="cuadro a"></span> A (Soporte) &nbsp;
        <span class="cuadro b"></span> B (Equipos) &nbsp;
        <span class="cuadro c"></span> C (Redes) &nbsp;
        <span class="cuadro d"></span> D (Mantenimiento)
    </div>

    <p style="margin-top:40px;font-style:italic;color:#555;">"La tecnología al servicio del medio ambiente para un Chiapas sustentable."</p>
</div>

{{-- ====================== DETALLE ====================== --}}
<div class="header">
    <img src="{{ public_path('images/logo_semahn2.png') }}" alt="Logo SEMAHN">
    <h3>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h3>
    <p>UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA</p>
    <h4>DETALLE DE FORMATOS REGISTRADOS</h4>
</div>

@foreach($formatos as $f)
    <table style="margin-bottom: 15px;">
        <tr>
            <th style="width: 15%;">Folio</th><td style="width: 35%;">{{ $f->folio ?? '—' }}</td>
            <th style="width: 15%;">Tipo</th><td class="tipo">{{ $f->tipo_formato ?? '—' }}</td>
        </tr>
        <tr>
            <th>Usuario</th><td>{{ $f->usuario ?? 'Sin usuario' }}</td>
            <th>Fecha</th><td>{{ $f->fecha ? \Carbon\Carbon::parse($f->fecha)->format('d/m/Y') : '—' }}</td>
        </tr>
        <tr><th>Descripción</th><td colspan="3">{{ $f->descripcion_servicio ?? 'Sin descripción' }}</td></tr>
        <tr><th>Observaciones</th><td colspan="3">{{ $f->diagnostico ?? $f->observaciones ?? 'N/A' }}</td></tr>
        <tr><th>Conclusión</th><td colspan="3">{{ $f->conclusion_servicio ?? 'N/A' }}</td></tr>
    </table>
    <div class="separador"></div>
@endforeach

{{-- ====================== TABLA MENSUAL ====================== --}}
@php
    $mensual = $formatos->groupBy(function($f){ return \Carbon\Carbon::parse($f->fecha)->format('Y-m'); })
                        ->map(fn($g)=>$g->count());
@endphp

<div class="resumen">
    <h3>Comparativa Mensual</h3>
    <table class="tabla-mensual">
        <tr><th>Mes</th><th>Total de Formatos</th></tr>
        @foreach($mensual as $mes=>$totalMes)
            <tr>
                <td>{{ \Carbon\Carbon::parse($mes.'-01')->translatedFormat('F Y') }}</td>
                <td>{{ $totalMes }}</td>
            </tr>
        @endforeach
    </table>

    <h3>Análisis general</h3>
    @php
        $promedio = $total > 0 ? round($total / max(1, $formatos->unique('fecha')->count()), 2) : 0;
        $maxTipo = collect(['A'=>$totalA,'B'=>$totalB,'C'=>$totalC,'D'=>$totalD])->sortDesc()->keys()->first();
    @endphp
    <p>Durante el periodo consultado se generaron un total de <strong>{{ $total }}</strong> formatos registrados en el sistema.</p>
    <p>El tipo de formato con mayor incidencia fue el <strong>Tipo {{ $maxTipo }}</strong>, representando el <strong>{{ pct(${'total'.$maxTipo}, $max) }}%</strong> del volumen total de trabajo.</p>
    <p>Este reporte refleja la continuidad de los servicios tecnológicos prestados por el área de informática a las diversas áreas de la Secretaría.</p>
</div>

{{-- ====================== ACTIVIDAD POR USUARIO ====================== --}}
@php
    $porUsuario = $formatos->groupBy('usuario')->map(function($group) {
        return [
            'total' => $group->count(),
            'A' => $group->where('tipo_formato','A')->count(),
            'B' => $group->where('tipo_formato','B')->count(),
            'C' => $group->where('tipo_formato','C')->count(),
            'D' => $group->where('tipo_formato','D')->count(),
        ];
    })->sortByDesc('total');
@endphp

<div class="resumen">
    <h3>Resumen de Actividad por Usuario</h3>

    <table class="tabla-mensual">
        <tr>
            <th style="text-align: left;">Nombre del Usuario</th>
            <th>Total Formatos</th>
            <th>Tipo A</th>
            <th>Tipo B</th>
            <th>Tipo C</th>
            <th>Tipo D</th>
            <th>% Part.</th>
        </tr>
        @foreach($porUsuario as $nombre => $u)
            @php
                $participacion = $total > 0 ? round(($u['total']/$total)*100,1) : 0;
            @endphp
            <tr>
                <td style="text-align: left;">{{ $nombre ?: 'Sin nombre asignado' }}</td>
                <td><strong>{{ $u['total'] }}</strong></td>
                <td>{{ $u['A'] }}</td>
                <td>{{ $u['B'] }}</td>
                <td>{{ $u['C'] }}</td>
                <td>{{ $u['D'] }}</td>
                <td>{{ $participacion }}%</td>
            </tr>
        @endforeach
    </table>

    <p style="margin-top:15px; font-size: 11px; color: #666;">
        * Este resumen muestra el desglose de documentos generados por cada integrante del equipo técnico en el periodo seleccionado.
    </p>
</div>

<div class="footer">
    <p>Generado por el Sistema de Formatos Digitales SEMAHN</p>
    <p>© {{ date('Y') }} Secretaría de Medio Ambiente e Historia Natural</p>
</div>

</body>
</html>
