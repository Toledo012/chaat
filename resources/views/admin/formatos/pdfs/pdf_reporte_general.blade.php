<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte General de Formatos - SEMAHN</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; margin: 25px; color: #000; }
  .portada { text-align: center; margin-top: 120px; }
  .portada img { width: 120px; margin-bottom: 20px; }
  .portada h1 { font-size: 22px; margin-bottom: 10px; }
  .portada h3 { font-size: 18px; margin-bottom: 5px; }
  .portada p { font-size: 13px; }

  .info-filtros { text-align: left; margin: 40px auto 0; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 10px; width: 80%; }

  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th, td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: top; }
  th { background-color: #e2e3e5; }
  .estadisticas { width: 80%; margin: 30px auto; font-size: 13px; }
  .grafico { width: 80%; margin: 20px auto; }
  .barra { display: flex; align-items: center; margin-bottom: 8px; }
  .etiqueta { width: 180px; text-align: right; padding-right: 10px; font-weight: bold; font-size: 11px; }
  .barra-contenedor { flex: 1; background-color: #f1f1f1; height: 16px; border-radius: 3px; overflow: hidden; }
  .relleno { height: 100%; text-align: right; color: #fff; font-size: 10px; padding-right: 4px; line-height: 16px; }
  .a { background-color: #0d6efd; }
  .b { background-color: #198754; }
  .c { background-color: #ffc107; color:#000; }
  .d { background-color: #dc3545; }

  .leyenda { width: 80%; margin: 10px auto; display: flex; justify-content: space-around; font-size: 12px; }
  .cuadro { width: 14px; height: 14px; margin-right: 5px; display: inline-block; border-radius: 2px; }
  .cuadro.a { background: #0d6efd; } .cuadro.b { background: #198754; } .cuadro.c { background: #ffc107; } .cuadro.d { background: #dc3545; }

  .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 25px; page-break-before: always; }
  .header img { width: 90px; float: left; }
  .tipo { font-weight: bold; color: #0a3622; }
  .separador { border-top: 2px solid #000; margin: 15px 0; }

  .footer { text-align: center; font-size: 11px; margin-top: 25px; border-top: 1px solid #000; padding-top: 5px; }

  .resumen { page-break-before: always; margin-top: 60px; text-align: justify; }
  .resumen h3 { text-align: center; margin-bottom: 20px; }

  /* Gráfico pastel (SVG) */
  svg { display: block; margin: 0 auto; }

  /* Tabla mensual */
  .tabla-mensual { width: 80%; margin: 30px auto; border-collapse: collapse; font-size: 12px; }
  .tabla-mensual th, .tabla-mensual td { border: 1px solid #000; padding: 6px; text-align: center; }
  .tabla-mensual th { background: #cfe2ff; }

</style>
</head>
<body>

{{-- ====================== PORTADA ====================== --}}
<div class="portada">
  <img src="{{ public_path('images/logo_semahn2.png') }}" alt="Logo SEMAHN">
  <h1>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h1>
  <h3>UNIDAD DE APOYO ADMINISTRATIVO</h3>
  <p>ÁREA DE INFORMÁTICA</p>
  <h2 style="margin-top: 60px;">📊 REPORTE GENERAL DE FORMATOS</h2>
  <p><strong>Fecha de emisión:</strong> {{ now()->format('d/m/Y H:i') }}</p>

  <div class="info-filtros">
    <p><strong>Filtros aplicados:</strong></p>
    <ul style="list-style:none;padding-left:0;">
      <li>🧾 <strong>Tipo:</strong> {{ $tipo ?? 'Todos' }}</li>
      <li>👤 <strong>Usuario:</strong> {{ $usuario ?? 'Todos' }}</li>
      <li>📅 <strong>Fecha:</strong> {{ $fecha ? \Carbon\Carbon::parse($fecha)->format('d/m/Y') : 'Todas' }}</li>
    </ul>
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
    <tr><th>Tipo de Formato</th><th>Total</th><th>%</th></tr>
    <tr><td>A - Soporte</td><td>{{ $totalA }}</td><td>{{ pct($totalA,$max) }}%</td></tr>
    <tr><td>B - Equipos</td><td>{{ $totalB }}</td><td>{{ pct($totalB,$max) }}%</td></tr>
    <tr><td>C - Redes</td><td>{{ $totalC }}</td><td>{{ pct($totalC,$max) }}%</td></tr>
    <tr><td>D - Mantenimiento Personal</td><td>{{ $totalD }}</td><td>{{ pct($totalD,$max) }}%</td></tr>
    <tr><th>Total general</th><th>{{ $total }}</th><th>100%</th></tr>
  </table>

  <div class="grafico">
    @foreach(['A','B','C','D'] as $t)
      @php $v = ${'total'.$t}; $p = pct($v,$max); @endphp
      <div class="barra">
        <div class="etiqueta">Formato {{ $t }}</div>
        <div class="barra-contenedor">
          <div class="relleno {{ strtolower($t) }}" style="width:{{ $p }}%">{{ $p }}%</div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- PASTEL SVG --}}
  <svg viewBox="0 0 32 32" width="180" height="180">
    @php
      $sum = max(1, $totalA + $totalB + $totalC + $totalD);
      $a1 = round(($totalA / $sum) * 100, 1);
      $a2 = round(($totalB / $sum) * 100, 1);
      $a3 = round(($totalC / $sum) * 100, 1);
      $a4 = 100 - ($a1 + $a2 + $a3);
    @endphp
    <circle r="16" cx="16" cy="16" fill="transparent" stroke="#0d6efd" stroke-width="32" stroke-dasharray="{{ $a1 }} {{ 100-$a1 }}" transform="rotate(-90) translate(-32)" />
    <circle r="16" cx="16" cy="16" fill="transparent" stroke="#198754" stroke-width="32" stroke-dasharray="{{ $a2 }} {{ 100-$a2 }}" stroke-dashoffset="-{{ $a1 }}" transform="rotate(-90) translate(-32)" />
    <circle r="16" cx="16" cy="16" fill="transparent" stroke="#ffc107" stroke-width="32" stroke-dasharray="{{ $a3 }} {{ 100-$a3 }}" stroke-dashoffset="-{{ $a1 + $a2 }}" transform="rotate(-90) translate(-32)" />
    <circle r="16" cx="16" cy="16" fill="transparent" stroke="#dc3545" stroke-width="32" stroke-dasharray="{{ $a4 }} {{ 100-$a4 }}" stroke-dashoffset="-{{ $a1 + $a2 + $a3 }}" transform="rotate(-90) translate(-32)" />
  </svg>

  <div class="leyenda">
    <div><span class="cuadro a"></span>A</div>
    <div><span class="cuadro b"></span>B</div>
    <div><span class="cuadro c"></span>C</div>
    <div><span class="cuadro d"></span>D</div>
  </div>

  <p style="margin-top:40px;font-style:italic;color:#555;">“La tecnología al servicio del medio ambiente para un Chiapas sustentable.”</p>
  <p>© {{ date('Y') }} SEMAHN - Gobierno del Estado de Chiapas</p>
</div>

{{-- ====================== DETALLE ====================== --}}
<div class="header">
  <img src="{{ public_path('images/logo_semahn2.png') }}" alt="Logo SEMAHN">
  <h3>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h3>
  <p>UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA</p>
  <h4>📋 DETALLE DE FORMATOS REGISTRADOS</h4>
</div>

@foreach($formatos as $f)
<table>
  <tr><th>Folio</th><td>{{ $f->folio ?? '—' }}</td><th>Tipo</th><td class="tipo">{{ $f->tipo_formato ?? '—' }}</td></tr>
  <tr><th>Usuario</th><td>{{ $f->usuario ?? 'Sin usuario' }}</td><th>Fecha</th><td>{{ $f->fecha ? \Carbon\Carbon::parse($f->fecha)->format('d/m/Y') : '—' }}</td></tr>
  <tr><th>Descripción del servicio</th><td colspan="3">{{ $f->descripcion_servicio ?? 'Sin descripción' }}</td></tr>
  <tr><th>Diagnóstico / Observaciones</th><td colspan="3">{{ $f->diagnostico ?? $f->observaciones ?? 'N/A' }}</td></tr>
  <tr><th>Trabajo realizado / Detalle</th><td colspan="3">{{ $f->trabajo_realizado ?? 'N/A' }}</td></tr>
  <tr><th>Conclusión / Estado</th><td colspan="3">{{ $f->conclusion_servicio ?? 'N/A' }}</td></tr>
</table>
<div class="separador"></div>
@endforeach

{{-- ====================== TABLA MENSUAL ====================== --}}
@php
  $mensual = $formatos->groupBy(function($f){ return \Carbon\Carbon::parse($f->fecha)->format('Y-m'); })
                      ->map(fn($g)=>$g->count());
@endphp

<div class="resumen">
  <h3>📆 Comparativa Mensual</h3>
  <table class="tabla-mensual">
    <tr><th>Mes</th><th>Total de Formatos</th></tr>
    @foreach($mensual as $mes=>$totalMes)
      <tr>
        <td>{{ \Carbon\Carbon::parse($mes.'-01')->translatedFormat('F Y') }}</td>
        <td>{{ $totalMes }}</td>
      </tr>
    @endforeach
  </table>

  <h3>📈 Análisis general</h3>
  @php
    $promedio = $total > 0 ? round($total / max(1, $formatos->unique('fecha')->count()), 2) : 0;
    $maxTipo = collect(['A'=>$totalA,'B'=>$totalB,'C'=>$totalC,'D'=>$totalD])->sortDesc()->keys()->first();
  @endphp
  <p>Durante el periodo consultado se generaron un total de <strong>{{ $total }}</strong> formatos.</p>
  <p>El promedio diario de emisión es de <strong>{{ $promedio }}</strong> formatos.</p>
  <p>El tipo de formato más frecuente fue <strong>{{ $maxTipo }}</strong>, representando aproximadamente el 
    <strong>{{ pct(${'total'.$maxTipo}, $max) }}%</strong> del total.</p>
  <p>Estos resultados reflejan las actividades y mantenimiento realizados por el área de informática, 
    evidenciando el compromiso institucional en la atención técnica y administrativa de los equipos, redes y servicios tecnológicos.</p>
</div>

{{-- ====================== RENDIMIENTO POR USUARIO ====================== --}}
@php
  $porUsuario = $formatos->groupBy('usuario')->map(function($group) {
      $total = $group->count();
      return [
          'total' => $total,
          'A' => $group->where('tipo_formato','A')->count(),
          'B' => $group->where('tipo_formato','B')->count(),
          'C' => $group->where('tipo_formato','C')->count(),
          'D' => $group->where('tipo_formato','D')->count(),
      ];
  })->sortByDesc('total');

  $promedioUsuario = $porUsuario->count() > 0 ? round($porUsuario->avg('total'),2) : 0;
@endphp

<div class="resumen">
  <h3>👥 Rendimiento por Usuario</h3>

  <table class="tabla-mensual">
    <tr>
      <th>Usuario</th>
      <th>Total</th>
      <th>Formato A</th>
      <th>Formato B</th>
      <th>Formato C</th>
      <th>Formato D</th>
      <th>Participación</th>
      <th>Rendimiento</th>
    </tr>
    @foreach($porUsuario as $nombre => $u)
      @php
        $participacion = $total > 0 ? round(($u['total']/$total)*100,1) : 0;
        $rendimiento = $promedioUsuario > 0 ? round(($u['total']/$promedioUsuario)*100,1) : 0;
      @endphp
      <tr>
        <td>{{ $nombre ?: 'Sin nombre' }}</td>
        <td><strong>{{ $u['total'] }}</strong></td>
        <td>{{ $u['A'] }}</td>
        <td>{{ $u['B'] }}</td>
        <td>{{ $u['C'] }}</td>
        <td>{{ $u['D'] }}</td>
        <td>{{ $participacion }}%</td>
        <td>
          {{ $rendimiento }}%
          @if($rendimiento > 120)
            🔺 Alto
          @elseif($rendimiento < 80)
            🔻 Bajo
          @else
            ⚖️ Promedio
          @endif
        </td>
      </tr>
    @endforeach
  </table>

  <p style="margin-top:15px;">
    El promedio general de desempeño es de <strong>{{ $promedioUsuario }}</strong> formatos por usuario.  
    Los usuarios con un rendimiento superior al 120% son considerados de <strong>alta productividad</strong>,  
    mientras que aquellos por debajo del 80% pueden requerir seguimiento o apoyo técnico adicional.
  </p>
</div>


<div class="footer">
  <p><em>Generado automáticamente por el Sistema de Formatos SEMAHN</em></p>
  <p><small>© {{ date('Y') }} Secretaría de Medio Ambiente e Historia Natural - Gobierno del Estado de Chiapas</small></p>
</div>

</body>
</html>
