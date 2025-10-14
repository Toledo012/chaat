<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Movimientos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Movimientos (auditoría)</h3>
        <a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Volver</a>
    </div>

    <form class="row g-2 mb-3" method="GET">
        <div class="col-md-2">
            <input type="text" class="form-control" name="tabla" placeholder="Tabla" value="{{ request('tabla') }}">
        </div>
        <div class="col-md-2">
            <select class="form-select" name="accion">
                <option value="">Acción</option>
                @foreach(['INSERT','UPDATE','DELETE'] as $a)
                    <option value="{{ $a }}" @selected(request('accion')===$a)>{{ $a }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" name="usuario" placeholder="Usuario" value="{{ request('usuario') }}">
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" name="desde" value="{{ request('desde') }}">
        </div>
        <div class="col-md-2">
            <input type="date" class="form-control" name="hasta" value="{{ request('hasta') }}">
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <div class="d-flex justify-content-end mb-2">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.movimientos.index', array_merge(request()->query(), ['export' => 1, 'autoprint' => 0])) }}" target="_blank">
            Exportar PDF (Imprimir)
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Tabla</th>
                        <th>Acción</th>
                        <th>ID Registro</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($movimientos as $m)
                    <tr>
                        <td>{{ $m->fecha }}</td>
                        <td>{{ $m->username ?? '—' }}</td>
                        <td>{{ $m->tabla }}</td>
                        <td><span class="badge bg-{{ $m->accion==='DELETE'?'danger':($m->accion==='UPDATE'?'warning text-dark':'success') }}">{{ $m->accion }}</span></td>
                        <td>{{ $m->id_registro }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#det{{ $m->id_movimiento }}">Ver</button>
                        </td>
                    </tr>
                    <tr class="collapse" id="det{{ $m->id_movimiento }}">
                        <td colspan="6">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Antes</h6>
                                    <pre class="bg-light p-2 small">{{ json_encode(json_decode($m->datos_anteriores ?? 'null', true), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                                <div class="col-md-6">
                                    <h6>Después</h6>
                                    <pre class="bg-light p-2 small">{{ json_encode(json_decode($m->datos_nuevos ?? 'null', true), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Sin movimientos</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $movimientos->links() }}</div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
