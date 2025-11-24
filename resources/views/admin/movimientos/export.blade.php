<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportar Movimientos</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin: 0 0 6px 0; }
        .muted { color: #666; font-size: 11px; }
        .row { display: flex; justify-content: space-between; align-items: flex-start; }
        .box { padding: 6px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
        th { background: #f2f2f2; text-align: left; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 11px; }
        .bg-success { background: #d4edda; }
        .bg-warning { background: #fff3cd; }
        .bg-danger { background: #f8d7da; }
        pre { white-space: pre-wrap; word-wrap: break-word; background: #fafafa; border: 1px solid #eee; padding: 6px; }
        @media print {
            .no-print { display: none; }
            a { color: inherit; text-decoration: none; }
        }
    </style>
    @if($autoprint)
    <script>window.addEventListener('load', () => window.print());</script>
    @endif
    <meta http-equiv="expires" content="0"/>
</head>
<body>
    <div class="row">
        <div class="box">
            <h1>Reporte de Movimientos</h1>
            <div class="muted">Generado: {{ now()->format('Y-m-d H:i:s') }}</div>
        </div>
        <div class="box" style="text-align:right">
            <div class="muted">
                @php $f = $filters ?? []; @endphp
                <div><strong>Tabla:</strong> {{ $f['tabla'] ?? 'Todas' }}</div>
                <div><strong>Acción:</strong> {{ $f['accion'] ?? 'Todas' }}</div>
                <div><strong>Usuario:</strong> {{ $f['usuario'] ?? 'Todos' }}</div>
                <div><strong>Desde:</strong> {{ $f['desde'] ?? '—' }} <strong>Hasta:</strong> {{ $f['hasta'] ?? '—' }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:130px">Fecha</th>
                <th style="width:120px">Usuario</th>
                <th style="width:120px">Tabla</th>
                <th style="width:80px">Acción</th>
                <th style="width:90px">ID Registro</th>
                <th>Antes</th>
                <th>Después</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimientos as $m)
                <tr>
                    <td>{{ $m->fecha }}</td>
                    <td>{{ $m->username ?? '—' }}</td>
                    <td>{{ $m->tabla }}</td>
                    <td>
                        @php
                            $cls = $m->accion==='DELETE' ? 'bg-danger' : ($m->accion==='UPDATE' ? 'bg-warning' : 'bg-success');
                        @endphp
                        <span class="badge {{ $cls }}">{{ $m->accion }}</span>
                    </td>
                    <td>{{ $m->id_registro }}</td>
                    <td><pre>{{ json_encode(json_decode($m->datos_anteriores ?? 'null', true), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></td>
                    <td><pre>{{ json_encode(json_decode($m->datos_nuevos ?? 'null', true), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:#888">Sin resultados</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="no-print" style="margin-top:12px">
        <button onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
</body>
</html>

