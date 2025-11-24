<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Vista previa - Formato B</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body{font-family:Arial,sans-serif;font-size:14px;background:#f8f9fa;padding:2rem;}
    .header{border-bottom:3px solid #157347;margin-bottom:20px;padding-bottom:10px;}
    .header img{width:110px;}
    .titulo{text-align:center;font-weight:bold;color:#0a3622;}
    .section-title{background:#d1e7dd;color:#0a3622;font-weight:bold;padding:6px;border-radius:4px;margin-top:1rem;}
    .firmas td{border:none;text-align:center;padding-top:30px;}
    .edicion{display:none;}
  </style>
</head>
<body>

<div class="container bg-white shadow p-4 rounded">

  {{-- BOTÓN EDITAR --}}
  <div class="text-end mb-3">
      <button class="btn btn-success" onclick="toggleEdicion()">
          <i class="fa-solid fa-pen-to-square"></i> Editar Formato
      </button>
  </div>

  {{-- FORMULARIO --}}
  <form method="POST" action="{{ route('admin.formatos.update', ['B', $servicio->id_servicio]) }}">
      @csrf

      {{-- ENCABEZADO --}}
      <div class="row align-items-center header">
        <div class="col-3 text-center">
          <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo SEMAHN">
        </div>
        <div class="col-9 text-center">
          <h5>SECRETARÍA DE MEDIO AMBIENTE E HISTORIA NATURAL</h5>
          <p class="mb-0">UNIDAD DE APOYO ADMINISTRATIVO - ÁREA DE INFORMÁTICA</p>
          <small><em>"2025, Año de Rosario Castellanos Figueroa"</em></small>
        </div>
      </div>

      <h5 class="titulo">Formato B - Equipos e Impresoras</h5>

      {{-- DATOS GENERALES --}}
      <div class="section-title">Datos Generales</div>
      <table class="table table-bordered">
        <tr>
            <th>Folio</th>
            <td>{{ $servicio->folio }}</td>
            <th>Fecha</th>
            <td>{{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</td>
        </tr>

        <tr>
            <th>Subtipo</th>
            <td class="vista">{{ $servicio->subtipo }}</td>

            <td colspan="2">
                <select class="form-select edicion" name="subtipo">
                    <option value="Computadora" {{ $servicio->subtipo=='Computadora'?'selected':'' }}>Computadora</option>
                    <option value="Impresora" {{ $servicio->subtipo=='Impresora'?'selected':'' }}>Impresora</option>
                </select>
            </td>
        </tr>
      </table>

      {{-- DESCRIPCIÓN --}}
      <div class="section-title">Descripción del Servicio</div>
      <p class="vista">{{ $servicio->descripcion_servicio }}</p>
      <textarea name="descripcion_servicio" class="form-control edicion">{{ $servicio->descripcion_servicio }}</textarea>



      {{-- ===================================== --}}
      {{-- COMPUTADORA --}}
      {{-- ===================================== --}}
      @if($servicio->subtipo === 'Computadora')
      <div class="section-title">Datos de la Computadora</div>
      <table class="table table-bordered">

        <tr>
          <th>Marca</th>
          <td class="vista">{{ $servicio->marca }}</td>
          <td colspan="2"><input type="text" class="form-control edicion" name="marca" value="{{ $servicio->marca }}"></td>
        </tr>

        <tr>
          <th>Modelo</th>
          <td class="vista">{{ $servicio->modelo }}</td>
          <td colspan="2"><input type="text" class="form-control edicion" name="modelo" value="{{ $servicio->modelo }}"></td>
        </tr>

        <tr>
          <th>Procesador</th>
          <td class="vista">{{ $servicio->procesador }}</td>
          <td colspan="2"><input type="text" class="form-control edicion" name="procesador" value="{{ $servicio->procesador }}"></td>
        </tr>

        <tr>
          <th>RAM</th>
          <td class="vista">{{ $servicio->ram }}</td>
          <td colspan="2">
            <select name="ram" class="form-select edicion">
              <option {{ $servicio->ram=='4 GB'?'selected':'' }}>4 GB</option>
              <option {{ $servicio->ram=='8 GB'?'selected':'' }}>8 GB</option>
              <option {{ $servicio->ram=='16 GB'?'selected':'' }}>16 GB</option>
              <option {{ $servicio->ram=='32 GB'?'selected':'' }}>32 GB</option>
              <option {{ $servicio->ram=='64 GB'?'selected':'' }}>64 GB</option>
            </select>
          </td>
        </tr>

        <tr>
          <th>Disco duro</th>
          <td class="vista">{{ $servicio->disco_duro }}</td>
          <td colspan="2">
            <select name="disco_duro" class="form-select edicion">
              <option {{ $servicio->disco_duro=='HDD 500 GB'?'selected':'' }}>HDD 500 GB</option>
              <option {{ $servicio->disco_duro=='HDD 1 TB'?'selected':'' }}>HDD 1 TB</option>
              <option {{ $servicio->disco_duro=='SSD 240 GB'?'selected':'' }}>SSD 240 GB</option>
              <option {{ $servicio->disco_duro=='SSD 480 GB'?'selected':'' }}>SSD 480 GB</option>
              <option {{ $servicio->disco_duro=='SSD 1 TB'?'selected':'' }}>SSD 1 TB</option>
            </select>
          </td>
        </tr>

        <tr>
          <th>Sistema Operativo</th>
          <td class="vista">{{ $servicio->sistema_operativo }}</td>
          <td colspan="2">
            <select name="sistema_operativo" class="form-select edicion">
              <option {{ $servicio->sistema_operativo=='Windows 10'?'selected':'' }}>Windows 10</option>
              <option {{ $servicio->sistema_operativo=='Windows 11'?'selected':'' }}>Windows 11</option>
              <option {{ $servicio->sistema_operativo=='Linux'?'selected':'' }}>Linux</option>
              <option {{ $servicio->sistema_operativo=='MacOS'?'selected':'' }}>MacOS</option>
              <option {{ $servicio->sistema_operativo=='Otro'?'selected':'' }}>Otro</option>
            </select>
          </td>
        </tr>

      </table>
      @endif



      {{-- ===================================== --}}
      {{-- IMPRESORA --}}
      {{-- ===================================== --}}
      @if($servicio->subtipo === 'Impresora')
      <div class="section-title">Datos de la Impresora</div>

      <table class="table table-bordered">

        <tr>
          <th>Marca</th>
          <td class="vista">{{ $servicio->marca }}</td>
          <td colspan="3"><input type="text" class="form-control edicion" name="marca" value="{{ $servicio->marca }}"></td>
        </tr>

        <tr>
          <th>Modelo</th>
          <td class="vista">{{ $servicio->modelo }}</td>
          <td colspan="3"><input type="text" class="form-control edicion" name="modelo" value="{{ $servicio->modelo }}"></td>
        </tr>

        <tr>
          <th>Diagnóstico</th>
          <td class="vista" colspan="3">{{ $servicio->diagnostico }}</td>
        </tr>

        <tr class="edicion">
          <td colspan="4">
            <textarea class="form-control" name="diagnostico">{{ $servicio->diagnostico }}</textarea>
          </td>
        </tr>

      </table>
      @endif




      {{-- ===================================== --}}
      {{-- DIAGNÓSTICO GENERAL --}}
      {{-- ===================================== --}}
      <div class="section-title">Diagnóstico y Trabajo Realizado</div>

      <p class="vista"><strong>Diagnóstico:</strong> {{ $servicio->diagnostico }}</p>
      <textarea name="diagnostico" class="form-control edicion">{{ $servicio->diagnostico }}</textarea>

      <p class="vista"><strong>Origen de falla:</strong> {{ $servicio->origen_falla }}</p>
      <select name="origen_falla" class="form-select edicion">
          <option {{ $servicio->origen_falla=='Desgaste natural'?'selected':'' }}>Desgaste natural</option>
          <option {{ $servicio->origen_falla=='Mala operación'?'selected':'' }}>Mala operación</option>
          <option {{ $servicio->origen_falla=='Otro'?'selected':'' }}>Otro</option>
      </select>

      <p class="vista"><strong>Trabajo Realizado:</strong> {{ $servicio->trabajo_realizado }}</p>
      <textarea name="trabajo_realizado" class="form-control edicion">{{ $servicio->trabajo_realizado }}</textarea>

      <p class="vista"><strong>Conclusión:</strong> {{ $servicio->conclusion_servicio }}</p>
      <textarea name="conclusion_servicio" class="form-control edicion">{{ $servicio->conclusion_servicio }}</textarea>




      {{-- ===================================== --}}
      {{-- MATERIALES --}}
      {{-- ===================================== --}}
      <div class="section-title">Materiales Utilizados</div>

      {{-- Vista NORMAL --}}
      @if($materiales->isEmpty())
        <p class="vista">No se registraron materiales.</p>
      @else
        <table class="table table-bordered vista">
          <thead>
            <tr><th>Material</th><th>Cantidad</th></tr>
          </thead>
          <tbody>
          @foreach($materiales as $mat)
            <tr>
              <td>{{ $mat->nombre }}</td>
              <td>{{ $mat->cantidad }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      @endif


      {{-- EDICIÓN --}}
      <table class="table table-bordered edicion" id="tablaMateriales">
        <thead class="table-light">
        <tr>
            <th>Material</th>
            <th>Cantidad</th>
            <th>Acción</th>
        </tr>
        </thead>
        <tbody>

        @foreach($materiales as $i => $mat)
        <tr>
          <td>
            <select name="materiales[{{ $i }}][id_material]" class="form-select">
                <option value="">Seleccionar</option>
                @foreach(DB::table('catalogo_materiales')->orderBy('nombre')->get() as $m)
                    <option value="{{ $m->id_material }}" {{ $mat->id_material == $m->id_material ? 'selected':'' }}>
                        {{ $m->nombre }}
                    </option>
                @endforeach
            </select>
          </td>

          <td>
            <input type="number" class="form-control" name="materiales[{{ $i }}][cantidad]"
                   value="{{ $mat->cantidad }}" min="1">
          </td>

          <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm eliminar-material">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
        @endforeach

        {{-- Fila vacía si no había materiales --}}
        @if($materiales->isEmpty())
        <tr>
          <td>
            <select name="materiales[0][id_material]" class="form-select">
                <option value="">Seleccionar material</option>
                @foreach(DB::table('catalogo_materiales')->orderBy('nombre')->get() as $m)
                    <option value="{{ $m->id_material }}">{{ $m->nombre }}</option>
                @endforeach
            </select>
          </td>

          <td>
            <input type="number" name="materiales[0][cantidad]" class="form-control" min="1">
          </td>

          <td class="text-center">
            <button type="button" class="btn btn-outline-success btn-sm agregar-material">
                <i class="fas fa-plus"></i>
            </button>
          </td>
        </tr>
        @endif

        </tbody>
      </table>




      {{-- ===================================== --}}
      {{-- OBSERVACIONES --}}
      {{-- ===================================== --}}
      <div class="section-title">Observaciones</div>
      <p class="vista">{{ $servicio->observaciones }}</p>
      <textarea name="observaciones" class="form-control edicion">{{ $servicio->observaciones }}</textarea>




      {{-- ===================================== --}}
      {{-- FIRMAS --}}
      {{-- ===================================== --}}
      <div class="section-title">Firmas de Conformidad</div>

      <table class="firmas" width="100%">
        <tr>
          <td>
              <strong>Usuario Solicitante</strong><br>
              <span class="vista">{{ $servicio->firma_usuario }}</span>
              <input type="text" class="form-control edicion" name="firma_usuario"
                     value="{{ $servicio->firma_usuario }}">
          </td>

          <td>
              <strong>Técnico</strong><br>
              <span class="vista">{{ $servicio->firma_tecnico }}</span>
              <input type="text" class="form-control edicion" name="firma_tecnico"
                     value="{{ $servicio->firma_tecnico }}">
          </td>

          <td>
              <strong>Jefe de Área</strong><br>
              <span class="vista">{{ $servicio->firma_jefe_area }}</span>
              <input type="text" class="form-control edicion" name="firma_jefe_area"
                     value="{{ $servicio->firma_jefe_area }}">
          </td>
        </tr>
      </table>




      {{-- BOTÓN GUARDAR --}}
      <div class="text-end mt-4 edicion" id="guardarBtn" style="display:none;">
          <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      </div>

      {{-- BOTÓN CANCELAR --}}
      <div class="text-end mt-2 vista">
          <a href="{{ route('admin.formatos.create') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>

  </form>

</div>




{{-- ===================================== --}}
{{-- SCRIPTS --}}
{{-- ===================================== --}}
<script>
function toggleEdicion(){
    document.querySelectorAll('.vista').forEach(v => v.style.display='none');
    document.querySelectorAll('.edicion').forEach(e => e.style.display='');
    document.getElementById('guardarBtn').style.display='block';
}

/// MATERIAL INDEX CORREGIDO
let materialIndex = {{ count($materiales) }};


/// AGREGAR / ELIMINAR MATERIALES
document.addEventListener('click', e => {

    // Agregar
    if(e.target.closest('.agregar-material')){
        const tbody = document.querySelector('#tablaMateriales tbody');

        materialIndex++;

        let fila = document.createElement('tr');
        fila.innerHTML = `
          <td>
            <select name="materiales[${materialIndex}][id_material]" class="form-select">
              <option value="">Seleccionar material</option>
              @foreach(DB::table('catalogo_materiales')->orderBy('nombre')->get() as $m)
                <option value="{{ $m->id_material }}">{{ $m->nombre }}</option>
              @endforeach
            </select>
          </td>

          <td>
            <input type="number" name="materiales[${materialIndex}][cantidad]" class="form-control" min="1">
          </td>

          <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm eliminar-material">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        `;

        tbody.appendChild(fila);
    }

    // Eliminar
    if(e.target.closest('.eliminar-material')){
        e.target.closest('tr').remove();
    }

});
</script>

</body>
</html>
