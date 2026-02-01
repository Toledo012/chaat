<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista previa - Formato C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{font-family:Arial;background:#f8f9fa;padding:2rem;}
        .header{border-bottom:3px solid #157347;padding-bottom:10px;margin-bottom:20px;}
        .header img{width:110px;}
        .titulo{text-align:center;font-weight:bold;color:#0a3622;}
        .subtitulo{text-align:center;font-size:14px;margin-bottom:15px;}
        .section-title{background:#d1e7dd;color:#0a3622;font-weight:bold;padding:6px;border-radius:4px;margin-top:1rem;}
        .edicion{display:none;}
        table td, table th { border:1px solid #dee2e6;padding:8px;}
        .footer{text-align:center;margin-top:2rem;padding-top:10px;font-size:13px;color:#6c757d;border-top:2px solid #ccc;}
    </style>
</head>

<body>

<div class="container bg-white shadow p-4 rounded">

    {{-- BOTONES SUPERIORES --}}
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary">
            ← Volver a Formatos
        </a>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" onclick="toggleEdicion()">
                ✏️ Editar
            </button>

            <a href="{{ route('admin.formatos.c.pdf', $servicio->id_servicio) }}"
               target="_blank"
               class="btn btn-danger">
                📄 PDF
            </a>
        </div>
    </div>

    
    <form method="POST" action="{{ route('admin.formatos.update', ['C', $servicio->id_servicio]) }}">
        @csrf

        {{-- ENCABEZADO --}}
        <div class="row align-items-center header">
            <div class="col-3 text-center">
                <img src="{{ asset('images/logo_semahn2.png') }}">
            </div>
            <div class="col-9 text-center">
                <h5>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h5>
                <p class="mb-0">UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA</p>
                <small><em>"2025, Año de Rosario Castellanos Figueroa"</em></small>
            </div>
        </div>

        <h5 class="titulo">Formato C - Redes / Telefonía</h5>
        <p class="subtitulo">Atención de servicios de redes o telefonía institucional</p>

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
                    {{ \App\Models\Departamento::find($servicio->id_departamento)->nombre ?? 'No asignado' }}
                </td>
            </tr>
        </table>

        {{-- TIPO RED --}}
        <div class="section-title">Tipo de Red</div>
        <p class="vista">{{ $servicio->tipo_red }}</p>
        <select name="tipo_red" class="form-select edicion">
            <option value="Red" {{ $servicio->tipo_red=='Red'?'selected':'' }}>Red</option>
            <option value="Telefonía" {{ $servicio->tipo_red=='Telefonía'?'selected':'' }}>Telefonía</option>
        </select>

        {{-- TIPO SERVICIO --}}
        <div class="section-title">Tipo de Servicio</div>
        <p class="vista">{{ $servicio->tipo_servicio }}</p>
        <select name="tipo_servicio" class="form-select edicion">
            <option value="Preventivo" {{ $servicio->tipo_servicio=='Preventivo'?'selected':'' }}>Preventivo</option>
            <option value="Correctivo" {{ $servicio->tipo_servicio=='Correctivo'?'selected':'' }}>Correctivo</option>
            <option value="Configuracion" {{ $servicio->tipo_servicio=='Configuracion'?'selected':'' }}>Configuración</option>
        </select>

        {{-- DESCRIPCIÓN --}}
        <div class="section-title">Descripción del Servicio</div>
        <p class="vista">{{ $servicio->descripcion_servicio }}</p>
        <textarea name="descripcion_servicio" class="form-control edicion">{{ $servicio->descripcion_servicio }}</textarea>

        {{-- DIAGNÓSTICO --}}
        <div class="section-title">Diagnóstico</div>
        <p class="vista">{{ $servicio->diagnostico }}</p>
        <textarea name="diagnostico" class="form-control edicion">{{ $servicio->diagnostico }}</textarea>

        {{-- TRABAJO REALIZADO --}}
        <div class="section-title">Trabajo Realizado</div>
        <p class="vista">{{ $servicio->trabajo_realizado }}</p>
        <textarea name="trabajo_realizado" class="form-control edicion">{{ $servicio->trabajo_realizado }}</textarea>

        {{-- MATERIALES --}}
        <div class="section-title">Materiales Utilizados</div>

        {{-- VISTA --}}
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

        {{-- EDICIÓN --}}
        <div class="edicion">
            <table class="table table-bordered" id="tabla-materiales">
                <thead>
                    <tr><th>Material</th><th>Cantidad</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    @foreach($materiales as $i => $m)
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
                                <input type="number"
                                       name="materiales[{{ $i }}][cantidad]"
                                       class="form-control"
                                       value="{{ $m->cantidad }}">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm eliminarFila">X</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="button" class="btn btn-primary btn-sm mt-2" id="btnAgregar">
                <i class="fa-solid fa-plus"></i> Agregar Material
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
                    <strong>Jefe de Área</strong><br>
                    <span class="vista">{{ $servicio->firma_jefe_area }}</span>
                    <input name="firma_jefe_area" class="form-control edicion" value="{{ $servicio->firma_jefe_area }}">
                </td>
            </tr>
        </table>

        {{-- BOTONES --}}
        <div id="guardarBtn" class="text-end mt-3" style="display:none;">
            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>

    </form>

    <div class="footer">Sistema de Formatos Digitales</div>
</div>

<script>
function toggleEdicion(){
    document.querySelectorAll('.vista').forEach(v => v.style.display='none');
    document.querySelectorAll('.edicion').forEach(e => e.style.display='block');
    document.getElementById('guardarBtn').style.display='block';
}

let contador = {{ count($materiales) }};

document.getElementById('btnAgregar').addEventListener('click', () => {
    const tbody = document.querySelector('#tabla-materiales tbody');
    const fila = `
        <tr>
            <td>
                <select name="materiales[${contador}][id_material]" class="form-select">
                    @foreach($catalogo_materiales as $mat)
                        <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="materiales[${contador}][cantidad]" class="form-control" value="1"></td>
            <td><button type="button" class="btn btn-danger btn-sm eliminarFila">X</button></td>
        </tr>`;
    tbody.insertAdjacentHTML('beforeend', fila);
    contador++;
});

document.addEventListener('click', e => {
    if (e.target.closest('.eliminarFila')) {
        e.target.closest('tr').remove();
    }
});
</script>

</body>
</html>
