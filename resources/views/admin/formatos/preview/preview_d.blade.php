<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista previa - Formato D</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { font-family: Arial, sans-serif; background:#f8f9fa; padding:2rem; }
        .header { border-bottom:3px solid #157347; margin-bottom:20px; padding-bottom:10px; }
        .header img { width:110px; }
        .titulo { text-align:center; font-weight:bold; color:#0a3622; }
        .section-title { background:#d1e7dd; padding:6px; font-weight:bold; color:#0a3622; border-radius:4px; margin-top:1rem; }
        .edicion { display:none; }
        table td, table th { border:1px solid #dee2e6; padding:8px; }
        .footer { text-align:center; font-size:13px; border-top:2px solid #ccc; padding-top:10px; color:#6c757d; margin-top:2rem; }
    </style>
</head>

<body>

<div class="container bg-white shadow p-4 rounded">

    {{-- VOLVER --}}
    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary mb-3">
        &larr; Volver
    </a>

    {{-- BOTÓN EDITAR --}}
    <div class="text-end mb-3">
        <button type="button" class="btn btn-success" onclick="toggleEdicion()">
            <i class="fa-solid fa-pen-to-square"></i> Editar Formato
        </button>
    </div>

    {{-- FORMULARIO --}}
    <form method="POST" action="{{ route('admin.formatos.update', ['D', $servicio->id_servicio]) }}">
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

        <h5 class="titulo">Formato D - Entrega / Recepción de Equipo</h5>

        {{-- DATOS --}}
        <div class="section-title">Datos del Servicio</div>
        <table class="table table-bordered">
            <tr>
                <th>Folio</th> <td>{{ $servicio->folio }}</td>
                <th>Fecha</th> <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
            </tr>
        </table>

        {{-- EQUIPO --}}
        <div class="section-title">Datos del Equipo</div>
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

                <th>Serie</th>
                <td class="vista">{{ $servicio->serie }}</td>
                <td class="edicion"><input name="serie" class="form-control" value="{{ $servicio->serie }}"></td>
            </tr>
        </table>


        {{-- OBSERVACIONES --}}
        <div class="section-title">Observaciones</div>
        <p class="vista">{{ $servicio->observaciones }}</p>
        <textarea name="observaciones" class="form-control edicion">{{ $servicio->observaciones }}</textarea>

        {{-- FIRMAS --}}
        <div class="section-title">Firmas</div>
        <table class="table">
            <tr>
                <td>
                    <b>Receptor</b><br>
                    <span class="vista">{{ $servicio->receptor }}</span>
                    <input name="receptor" class="form-control edicion" value="{{ $servicio->receptor }}">
                </td>

                <td>

                <td>
                    <b>Jefe Área</b><br>
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

    <div class="footer">
        Generado desde el Sistema de Formatos Digitales
    </div>
</div>

<script>
function toggleEdicion() {
    document.querySelectorAll('.vista').forEach(v => v.style.display='none');
    document.querySelectorAll('.edicion').forEach(e => e.style.display='block');
    document.getElementById('guardarBtn').style.display='block';
}
</script>

</body>
</html>
