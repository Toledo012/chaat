<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formato D - Equipos Personales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { font-family: Arial, sans-serif; background:#f8f9fa; padding:2rem; }
        .header { text-align:center; border-bottom:3px solid #157347; padding-bottom:10px; margin-bottom:20px; }
        .header-logo { width:120px; }
        .titulo { font-weight:bold; color:#0a3622; text-transform:uppercase; margin-top:1rem; }
        .section-title { background:#d1e7dd; padding:6px; font-weight:bold; color:#0a3622; border-radius:4px; margin-top:1rem; }
        .edicion { display:none; }
        table th, table td { border:1px solid #ccc; padding:8px; }
        .firma-linea { margin-top:40px; text-align:center; }
        .footer { text-align:center; margin-top:2rem; border-top:2px solid #ccc; padding-top:10px; font-size:13px; color:#6c757d; }
    </style>
</head>

<body>

<div class="container bg-white p-4 shadow rounded">

    {{-- Volver --}}
    <a href="{{ route('admin.formatos.index') }}" class="btn btn-outline-secondary mb-3">&larr; Volver</a>

    {{-- Botón Editar --}}
    <div class="text-end mb-3">
        <button type="button" class="btn btn-success" onclick="toggleEdicion()">
            <i class="fa-solid fa-pen-to-square"></i> Editar Formato
        </button>
    </div>

    <form method="POST" action="{{ route('admin.formatos.update', ['D', $servicio->id_servicio]) }}">
        @csrf

        {{-- ENCABEZADO INSTITUCIONAL --}}
        <div class="header">
            <img src="{{ asset('images/logo_semahn2.png') }}" class="header-logo"><br>
            <strong>UNIDAD DE APOYO ADMINISTRATIVO<br>ÁREA DE INFORMÁTICA</strong><br>
            <em>"2025, Año de Rosario Castellanos Figueroa"</em>
        </div>

        <h4 class="titulo">FORMATO D - MANTENIMIENTO EQUIPOS PERSONALES</h4>

        {{-- FECHA --}}
        <div class="section-title">Fecha</div>

        <p class="vista">
            {{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}
        </p>

        <input type="date" name="fecha" class="form-control edicion"
               value="{{ $servicio->fecha }}">

        {{-- CUERPO DEL DOCUMENTO --}}
        <p class="mt-3 vista">
            El C. <strong>{{ $servicio->otorgante }}</strong> entrega el equipo con las siguientes características:
        </p>

        <div class="edicion">
            <label>Nombre del Otorgante:</label>
            <input type="text" name="otorgante" class="form-control" value="{{ $servicio->otorgante }}">
        </div>

        {{-- DATOS DE EQUIPO --}}
        <div class="section-title">Datos del Equipo</div>

        <table class="table table-bordered">
            <tr>
                <th>EQUIPO</th>
                <td class="vista">{{ $servicio->equipo }}</td>
                <td class="edicion"><input name="equipo" class="form-control" value="{{ $servicio->equipo }}"></td>
            </tr>

            <tr>
                <th>MARCA</th>
                <td class="vista">{{ $servicio->marca }}</td>
                <td class="edicion"><input name="marca" class="form-control" value="{{ $servicio->marca }}"></td>
            </tr>

            <tr>
                <th>MODELO</th>
                <td class="vista">{{ $servicio->modelo }}</td>
                <td class="edicion"><input name="modelo" class="form-control" value="{{ $servicio->modelo }}"></td>
            </tr>

            <tr>
                <th>SERIE</th>
                <td class="vista">{{ $servicio->serie }}</td>
                <td class="edicion"><input name="serie" class="form-control" value="{{ $servicio->serie }}"></td>
            </tr>
        </table>

        {{-- TEXTO OFICIAL --}}
        <p class="vista">
            Sirva el presente formato como comprobante de entrega del equipo mencionado anteriormente,
            que pertenece al C. <strong>{{ $servicio->otorgante }}</strong>, al personal del Área de Informática
            de la Secretaría de Medio Ambiente e Historia Natural, que se compromete a realizar el servicio
            de manera cuidadosa y profesional.
        </p>

        {{-- RECEPTOR --}}
        <p class="vista">
            Nombre y firma del receptor: <strong>{{ $servicio->receptor }}</strong>
        </p>

        <div class="edicion">
            <label>Nombre del Receptor:</label>
            <input type="text" name="receptor" class="form-control" value="{{ $servicio->receptor }}">
        </div>

        {{-- OBSERVACIONES --}}
        <div class="section-title">Observaciones</div>

        <p class="vista">{{ $servicio->observaciones }}</p>

        <textarea name="observaciones" class="form-control edicion">{{ $servicio->observaciones }}</textarea>

        {{-- FIRMAS --}}
        <div class="row mt-5">
            <div class="col text-center">
                <div class="firma-linea">______________________________</div>
                <b>OTORGANTE</b><br>
                <span class="vista">{{ $servicio->otorgante }}</span>
            </div>

            <div class="col text-center">
                <div class="firma-linea">______________________________</div>
                <b>RECEPTOR</b><br>
                <span class="vista">{{ $servicio->receptor }}</span>
            </div>

            <div class="col text-center">
                <div class="firma-linea">______________________________</div>
                <b>JEFE DE ÁREA</b><br>
                <span class="vista">{{ $servicio->firma_jefe_area }}</span>

                <input class="form-control edicion mt-2"
                       name="firma_jefe_area"
                       value="{{ $servicio->firma_jefe_area }}">
            </div>
        </div>

        {{-- Botón Guardar --}}
        <div id="guardarBtn" class="text-end mt-4" style="display:none;">
            <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>

    </form>

    <div class="footer">
        Generado desde el Sistema de Formatos Digitales
    </div>

</div>

<script>
function toggleEdicion(){
    document.querySelectorAll('.vista').forEach(v => v.style.display='none');
    document.querySelectorAll('.edicion').forEach(e => e.style.display='block');
    document.getElementById('guardarBtn').style.display='block';
}
</script>

</body>
</html>
