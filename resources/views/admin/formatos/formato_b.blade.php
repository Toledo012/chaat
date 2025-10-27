<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Formato B - Equipos e Impresoras</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container">
  <div class="card shadow border-0">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Formato B - Equipos / Impresoras</h5>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('admin.formatos.b.store') }}">
        @csrf

        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Subtipo de servicio</label>
            <select name="subtipo" id="subtipo" class="form-select" required>
              <option value="">Seleccionar</option>
              <option value="Computadora">Computadora</option>
              <option value="Impresora">Impresora</option>
            </select>
          </div>
          <div class="col-md-8">
            <label class="form-label">Descripción del servicio</label>
            <input type="text" name="descripcion_servicio" class="form-control"
                   placeholder="Ej. mantenimiento correctivo de laptop o impresora">
          </div>
        </div>

        {{-- =======================================================
             BLOQUE COMPUTADORA
        ======================================================= --}}
        <div id="bloque_computadora" style="display:none;">
          <h6 class="text-success"><i class="fas fa-laptop me-2"></i>Datos de la computadora</h6>

          <div class="row mb-3">
            <div class="col-md-3">
              <label class="form-label">Marca</label>
              <select name="marca" class="form-select">
                <option value="">Seleccionar marca</option>
                <option>HP</option>
                <option>Dell</option>
                <option>Lenovo</option>
                <option>Asus</option>
                <option>Acer</option>
                <option>Apple</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Modelo / Año</label>
              <select name="modelo" class="form-select">
                <option value="">Seleccionar año</option>
                @for ($i = now()->year; $i >= now()->year - 10; $i--)
                  <option value="{{ $i }}">{{ $i }}</option>
                @endfor
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">RAM</label>
              <select name="ram" class="form-select">
                <option value="">Seleccionar</option>
                <option>4 GB</option>
                <option>8 GB</option>
                <option>12 GB</option>
                <option>16 GB</option>
                <option>24 GB</option>
                <option>32 GB</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Disco duro</label>
              <select name="disco_duro" class="form-select">
                <option value="">Seleccionar</option>
                <option>120 GB SSD</option>
                <option>240 GB SSD</option>
                <option>480 GB SSD</option>
                <option>1 TB HDD</option>
                <option>2 TB HDD</option>
              </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3">
              <label class="form-label">Procesador</label>
              <input type="text" name="procesador" class="form-control" placeholder="Ej. Intel i5 / Ryzen 5">
            </div>
            <div class="col-md-3">
              <label class="form-label">Sistema operativo</label>
              <input type="text" name="sistema_operativo" class="form-control" placeholder="Ej. Windows 10 Pro">
            </div>
            <div class="col-md-3">
              <label class="form-label">N° Serie</label>
              <input type="text" name="numero_serie" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">N° Inventario</label>
              <input type="text" name="numero_inventario" class="form-control">
            </div>
          </div>
        </div>

        {{-- =======================================================
             BLOQUE IMPRESORA (SIN TONER / TIPO IMPRESIÓN)
        ======================================================= --}}
        <div id="bloque_impresora" style="display:none;">
          <h6 class="text-info"><i class="fas fa-print me-2"></i>Datos de la impresora</h6>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Marca</label>
              <select name="marca_impresora" class="form-select">
                <option value="">Seleccionar marca</option>
                <option>HP</option>
                <option>Canon</option>
                <option>Brother</option>
                <option>Epson</option>
                <option>Samsung</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Modelo</label>
              <input type="text" name="modelo_impresora" class="form-control" placeholder="Ej. LaserJet P1102 / G2110">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Diagnóstico / Observaciones</label>
            <textarea name="diagnostico_impresora" class="form-control" rows="2"></textarea>
          </div>
        </div>

        {{-- ✅ Script mejorado para mostrar/ocultar y limpiar --}}
        <script>
        document.getElementById('subtipo').addEventListener('change', function() {
          const tipo = this.value;
          const comp = document.getElementById('bloque_computadora');
          const imp = document.getElementById('bloque_impresora');

          // Mostrar bloque correspondiente
          comp.style.display = (tipo === 'Computadora') ? 'block' : 'none';
          imp.style.display = (tipo === 'Impresora') ? 'block' : 'none';

          // Limpiar valores del bloque oculto
          if (tipo === 'Computadora') {
            imp.querySelectorAll('input, select, textarea').forEach(el => el.value = '');
          } else if (tipo === 'Impresora') {
            comp.querySelectorAll('input, select, textarea').forEach(el => el.value = '');
          }
        });
        </script>

        <hr>
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
<h6><i class="fas fa-wrench me-2"></i>Detalles técnicos del servicio</h6>

<div class="row mb-3">
  <div class="col-md-4">
    <label class="form-label">Tipo de servicio</label>
    <select name="tipo_servicio" class="form-select">
      <option value="">Seleccionar</option>
      <option>Preventivo</option>
      <option>Correctivo</option>
      <option>Instalación</option>
      <option>Configuración</option>
      <option>Diagnóstico</option>
    </select>
  </div>

  <div class="col-md-8">
    <label class="form-label">Diagnóstico general</label>
    <textarea name="diagnostico" class="form-control" rows="2"
              placeholder="Describe brevemente el diagnóstico del problema detectado"></textarea>
  </div>
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


  <div class="col-md-6">
    <label class="form-label">Trabajo realizado</label>
    <input type="text" name="trabajo_realizado" class="form-control"
           placeholder="Ej. Sustitución de componente, reinstalación, limpieza, etc.">
  </div>
</div>

<div class="mb-3">
  <label class="form-label">Conclusión del servicio</label>
  <select name="conclusion_servicio" class="form-select">
    <option value="">Seleccionar</option>
    <option>Terminado</option>
    <option>En proceso</option>
    <option>No se pudo completar</option>
  </select>
</div>

        <hr>
        <div class="mb-3">
          <label class="form-label">Detalle del servicio realizado</label>
          <textarea name="detalle_realizado" class="form-control" rows="3"></textarea>
        </div>

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
          <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
          <a href="{{ route('admin.formatos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>

      </form>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
