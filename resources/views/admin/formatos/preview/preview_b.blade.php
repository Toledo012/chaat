<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista previa - Formato B</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { font-family: Arial, sans-serif; background:#f8f9fa; padding:2rem; }
        .header { border-bottom:3px solid #157347; margin-bottom:20px; }
        .header img { width:110px; }
        .titulo { text-align:center; font-weight:bold; color:#0a3622; }
        .section-title { background:#d1e7dd; padding:6px; font-weight:bold; color:#0a3622; border-radius:4px; margin-top:1rem; }
        .edicion { display:none; }
        .footer { text-align:center; font-size:13px; color:#6c757d; border-top:2px solid #ccc; padding-top:10px; margin-top:2rem; }
        table td, table th { padding:8px; border:1px solid #dee2e6; }
    </style>
</head>

<body>

<div class="container bg-white shadow p-4 rounded">

{{-- BOTONES --}}
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">← Volver</a>

    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" onclick="toggleEdicion()">✏️ Editar</button>
        <a href="{{ route('admin.formatos.b.pdf', $servicio->id_servicio) }}" target="_blank" class="btn btn-danger">📄 PDF</a>
    </div>
</div>

<form method="POST" action="{{ route('admin.formatos.update', ['B', $servicio->id_servicio]) }}">
@csrf

{{-- ENCABEZADO --}}
<div class="row header align-items-center">
    <div class="col-3 text-center">
        <img src="{{ asset('images/logo_semahn2.png') }}">
    </div>
    <div class="col-9 text-center">
        <h5>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h5>
        <p class="mb-0">ÁREA DE INFORMÁTICA</p>
    </div>
</div>

<h5 class="titulo">Formato B - Equipos de Cómputo o Impresoras</h5>

{{-- DATOS GENERALES --}}
<div class="section-title">Datos del Servicio</div>
<table class="table table-bordered">
    <tr>
                <th width="25%">Folio</th>
                <td>{{ $servicio->folio }}</td>
        <th>Fecha</th>
        <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <th>Departamento</th>
        <td colspan="3">
            <span class="vista">
                {{ $departamentos->firstWhere('id_departamento', $servicio->id_departamento)?->nombre ?? 'No asignado' }}
            </span>
            <select name="id_departamento" class="form-select edicion">
                @foreach($departamentos as $dep)
                    <option value="{{ $dep->id_departamento }}" {{ $dep->id_departamento == $servicio->id_departamento ? 'selected' : '' }}>
                        {{ $dep->nombre }}
                    </option>
                @endforeach
            </select>
        </td>
    </tr>
</table>

{{-- SUBTIPO --}}
<div class="section-title">Tipo de Equipo</div>
<p class="vista">{{ $servicio->subtipo }}</p>
<select name="subtipo" class="form-select edicion">
    <option value="Computadora" {{ $servicio->subtipo=='Computadora'?'selected':'' }}>Computadora</option>
    <option value="Impresora" {{ $servicio->subtipo=='Impresora'?'selected':'' }}>Impresora</option>
</select>

{{-- DESCRIPCIÓN --}}
<div class="section-title">Descripción del Servicio</div>
<p class="vista">{{ $servicio->descripcion_servicio }}</p>
<textarea name="descripcion_servicio" class="form-control edicion">{{ $servicio->descripcion_servicio }}</textarea>

{{-- DETALLES DEL EQUIPO --}}
<div class="section-title">Detalles del Equipo</div>
<table class="table table-bordered">
<tr>
    <th>Equipo</th>
    <td class="vista">{{ $servicio->equipo }}</td>
    <td class="edicion"><input name="equipo" class="form-control" value="{{ $servicio->equipo }}"></td>

    <th>Marca</th>
    <td class="vista">{{ $servicio->marca }}</td>
    <td class="edicion"><input name="marca" class="form-control" value="{{ $servicio->marca }}"></td>
</tr>
<tr>
    <th>Modelo</th>
    <td class="vista">{{ $servicio->modelo }}</td>
    <td class="edicion"><input name="modelo" class="form-control" value="{{ $servicio->modelo }}"></td>

    <th>No. Inventario</th>
    <td class="vista">{{ $servicio->numero_inventario }}</td>
    <td class="edicion"><input name="numero_inventario" class="form-control" value="{{ $servicio->numero_inventario }}"></td>
</tr>
<tr>
    <th>No. Serie</th>
    <td class="vista">{{ $servicio->numero_serie }}</td>
    <td class="edicion"><input name="numero_serie" class="form-control" value="{{ $servicio->numero_serie }}"></td>

    <th>Sistema Operativo</th>
    <td class="vista">{{ $servicio->sistema_operativo }}</td>
    <td class="edicion"><input name="sistema_operativo" class="form-control" value="{{ $servicio->sistema_operativo }}"></td>
</tr>
<tr>
    <th>Procesador</th>
    <td class="vista">{{ $servicio->procesador }}</td>
    <td class="edicion"><input name="procesador" class="form-control" value="{{ $servicio->procesador }}"></td>

    <th>RAM</th>
    <td class="vista">{{ $servicio->ram }}</td>
    <td class="edicion"><input name="ram" class="form-control" value="{{ $servicio->ram }}"></td>
</tr>
<tr>
    <th>Disco Duro</th>
    <td class="vista">{{ $servicio->disco_duro }}</td>
    <td class="edicion"><input name="disco_duro" class="form-control" value="{{ $servicio->disco_duro }}"></td>

    <th>Origen de Falla</th>
    <td class="vista">{{ $servicio->origen_falla }}</td>
    <td class="edicion">
        <select name="origen_falla" class="form-select">
            @foreach(['Desgaste natural','Mala operación','Otro'] as $op)
                <option {{ $servicio->origen_falla==$op?'selected':'' }}>{{ $op }}</option>
            @endforeach
        </select>
    </td>
</tr>
</table>

{{-- DIAGNÓSTICO --}}
<div class="section-title">Diagnóstico</div>
<p class="vista">{{ $servicio->diagnostico }}</p>
<textarea name="diagnostico" class="form-control edicion">{{ $servicio->diagnostico }}</textarea>

{{-- TRABAJO --}}
<div class="section-title">Trabajo Realizado</div>
<p class="vista">{{ $servicio->trabajo_realizado }}</p>
<textarea name="trabajo_realizado" class="form-control edicion">{{ $servicio->trabajo_realizado }}</textarea>

{{-- DETALLE --}}
<div class="section-title">Detalle del Trabajo Realizado</div>
<p class="vista">{{ $servicio->detalle_realizado }}</p>
<textarea name="detalle_realizado" class="form-control edicion">{{ $servicio->detalle_realizado }}</textarea>

{{-- CONCLUSIÓN --}}
<div class="section-title">Conclusión</div>
<p class="vista">{{ $servicio->conclusion_servicio }}</p>
<textarea name="conclusion_servicio" class="form-control edicion">{{ $servicio->conclusion_servicio }}</textarea>


        {{-- MATERIALES --}}
        <div class="section-title">Materiales Utilizados</div>

        {{-- Vista --}}
        <table class="table table-bordered vista">
            <thead>
                <tr><th>Material</th><th>Cantidad</th></tr>
            </thead>
            <tbody>
                @forelse($materiales as $m)
                    <tr>
                        <td>{{ $m->nombre }}</td>
                        <td>{{ $m->cantidad }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-center">Sin materiales</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- EDICIÓN DE MATERIALES --}}
        <div class="edicion">
            <table class="table table-bordered" id="tabla-materiales">
                <thead>
                    <tr><th>Material</th><th>Cantidad</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    @foreach ($materiales as $i => $m)
                        <tr>
                            <td>
                                <select name="materiales[{{ $i }}][id_material]" class="form-select">
                                    @foreach($catalogo_materiales as $mat)
                                        <option value="{{ $mat->id_material }}"
                                            {{ $mat->id_material == $m->id_material ? 'selected' : '' }}>
                                            {{ $mat->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control"
                                       name="materiales[{{ $i }}][cantidad]"
                                       value="{{ $m->cantidad }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm eliminarFila">X</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="button" class="btn btn-primary btn-sm mt-2" id="btnAgregar">
                + Agregar Material
            </button>
        </div>
{{-- OBSERVACIONES --}}
<div class="section-title">Observaciones</div>
<p class="vista">{{ $servicio->observaciones }}</p>
<textarea name="observaciones" class="form-control edicion">{{ $servicio->observaciones }}</textarea>

{{-- FIRMAS --}}
<div class="section-title">Firmas</div>
<table class="table">
<tr>
<td>
    <strong>Usuario</strong><br>
    <span class="vista">{{ $servicio->firma_usuario }}</span>
    <input name="firma_usuario" class="form-control edicion" value="{{ $servicio->firma_usuario }}">
</td>
<td>
    <strong>Técnico</strong><br>
    <span class="vista">{{ $servicio->firma_tecnico }}</span>
    <input name="firma_tecnico" class="form-control edicion" value="{{ $servicio->firma_tecnico }}">
</td>
<td>
    <strong>Jefe Área</strong><br>
    <span class="vista">{{ $servicio->firma_jefe_area }}</span>
    <input name="firma_jefe_area" class="form-control edicion" value="{{ $servicio->firma_jefe_area }}">
</td>
</tr>
</table>

<div id="guardarBtn" class="text-end mt-3" style="display:none;">
    <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">Cancelar</button>
    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
</div>

</form>

<div class="footer">Generado desde el Sistema de Formatos Digitales</div>

</div>

<script>
function toggleEdicion() {
    document.querySelectorAll('.vista').forEach(v => v.style.display = 'none');
    document.querySelectorAll('.edicion').forEach(e => e.style.display = 'block');
    document.getElementById('guardarBtn').style.display = 'block';
}

let contador = {{ count($materiales) }};

document.getElementById('btnAgregar').addEventListener('click', function() {
    let tabla = document.querySelector('#tabla-materiales tbody');

    let fila = `
        <tr>
            <td>
                <select name="materiales[${contador}][id_material]" class="form-select">
                    @foreach($catalogo_materiales as $mat)
                        <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="materiales[${contador}][cantidad]" class="form-control" value="1">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm eliminarFila">X</button>
            </td>
        </tr>
    `;

    tabla.insertAdjacentHTML('beforeend', fila);
    contador++;
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.eliminarFila')) {
        e.target.closest('tr').remove();
    }
});
</script>

</body>
</html>
