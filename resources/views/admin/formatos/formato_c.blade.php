<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Formato C - Redes y Telefonía</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container">
  <div class="card shadow border-0">
    <div class="card-header bg-info text-white">
      <h5 class="mb-0"><i class="fas fa-network-wired me-2"></i>Formato C - Redes y Telefonía</h5>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('admin.formatos.c.store') }}">
        @csrf

<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Descripción del servicio</label>
    <input type="text" name="descripcion_servicio" class="form-control"
           placeholder="Ej. instalación o mantenimiento de red, revisión de puntos, etc.">
  </div>

  <div class="col-md-3">
    <label class="form-label">Tipo de red</label>
    <select name="tipo_red" class="form-select" required>
      <option value="">Seleccionar</option>
      <option value="Red">Red</option>
      <option value="Telefonía">Telefonía</option>
    </select>
  </div>

  <div class="col-md-3">
    <label class="form-label">Tipo de servicio</label>
    <select name="tipo_servicio" class="form-select" required>
      <option value="">Seleccionar</option>
      <option value="Preventivo">Preventivo</option>
      <option value="Correctivo">Correctivo</option>
      <option value="Configuracion">Configuración</option>
    </select>
  </div>
</div>
<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Diagnóstico</label>
    <textarea name="diagnostico" class="form-control" rows="2"
              placeholder="Describe brevemente el problema detectado o la falla observada"></textarea>
  </div>

  <div class="col-md-6">
    <label class="form-label">Origen de la falla</label>
    <select name="origen_falla" class="form-select">
      <option value="">Seleccionar</option>
      <option value="Desgaste natural">Desgaste natural</option>
      <option value="Mala operación">Mala operación</option>
      <option value="Otro">Otro</option>
    </select>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label">Trabajo realizado</label>
    <textarea name="trabajo_realizado" class="form-control" rows="2"
              placeholder="Describe brevemente las acciones realizadas (ej. reparación, sustitución, configuración)"></textarea>
  </div>

  <div class="col-md-6">
    <label class="form-label">Detalle del servicio realizado</label>
    <textarea name="detalle_realizado" class="form-control" rows="2"
              placeholder="Detalles adicionales sobre la intervención o componentes revisados"></textarea>
  </div>
</div>




        <hr>
        <button class="btn btn-outline-secondary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#bloqueMateriales">
          <i class="fas fa-boxes me-1"></i> Agregar materiales (opcional)
        </button>

        <div class="collapse" id="bloqueMateriales">
          <h6><i class="fas fa-cogs me-2"></i>Materiales utilizados</h6>
          <table class="table table-bordered" id="tablaMateriales">
            <thead class="table-light">
              <tr>
                <th>Material</th>
                  
                <th>Cantidad</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <select name="materiales[0][id_material]" class="form-select">
                    <option value="">Seleccionar material</option>
                    @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                      <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                    @endforeach
                  </select>
                </td>
                <td><input type="number" name="materiales[0][cantidad]" class="form-control" min="1" step="1"></td>
                <td class="text-center">
                  <button type="button" class="btn btn-outline-success btn-sm" onclick="agregarFila()">+</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <script>
        let contador = 1;
        function agregarFila() {
          const tabla = document.querySelector('#tablaMateriales tbody');
          const fila = document.createElement('tr');
          fila.innerHTML = `
            <td>
              <select name="materiales[${contador}][id_material]" class="form-select">
                <option value="">Seleccionar material</option>
                @foreach(\DB::table('catalogo_materiales')->orderBy('nombre')->get() as $mat)
                  <option value="{{ $mat->id_material }}">{{ $mat->nombre }}</option>
                @endforeach
              </select>
            </td>
            <td><input type="number" name="materiales[${contador}][cantidad]" class="form-control" min="1" step="1"></td>
            <td class="text-center">
              <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()">−</button>
            </td>
          `;
          tabla.appendChild(fila);
          contador++;
        }
        </script>

        <hr>
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Usuario / Solicitante</label>
            <input name="firma_usuario" class="form-control" placeholder="Nombre del solicitante">
          </div>
          <div class="col-md-4">
            <label class="form-label">Técnico</label>
            <input name="firma_tecnico" class="form-control"
                   value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}" readonly>
          </div>
          <div class="col-md-4">
            <label class="form-label">Jefe de Área</label>
            <input name="firma_jefe_area" class="form-control"
                   value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}" readonly>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Observaciones</label>
          <textarea name="observaciones" class="form-control" rows="2"></textarea>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Guardar</button>
          <a href="{{ route('admin.formatos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>

      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
