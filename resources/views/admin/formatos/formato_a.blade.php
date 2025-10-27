<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Formato A - Desarrollo / Soporte</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container">
  <div class="card shadow border-0">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0"><i class="fas fa-laptop-code me-2"></i>Formato A - Desarrollo / Soporte</h5>
    </div>
    <div class="card-body">

    <form method="POST" action="{{ route('admin.formatos.a.store') }}">
    @csrf


        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Subtipo</label>
            <select name="subtipo" class="form-select">
              <option value="Desarrollo">Desarrollo</option>
              <option value="Soporte">Soporte</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tipo de Atención</label>
            <select name="tipo_atencion" class="form-select">
              <option value="Memo">Memo</option>
              <option value="Teléfono">Teléfono</option>
              <option value="Jefe">Jefe</option>
              <option value="Usuario">Usuario</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tipo de Servicio</label>
            <select name="tipo_servicio" class="form-select">
              <option>Equipos</option>
              <option>Redes LAN/WAN</option>
              <option>Antivirus</option>
              <option>Software</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Petición del Usuario</label>
          <textarea class="form-control" name="peticion" rows="3"></textarea>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Trabajo Realizado</label>
            <select name="trabajo_realizado" class="form-select">
              <option>En sitio</option>
              <option>Área de producción</option>
              <option>Traslado de equipo</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Conclusión del Servicio</label>
            <select name="conclusion_servicio" class="form-select">
              <option>Terminado</option>
              <option>En proceso</option>
            </select>
          </div>
        </div>
<div class="mb-3">
  <label class="form-label">Detalle del servicio realizado</label>
  <textarea name="detalle_realizado" class="form-control" rows="3" placeholder="Describe brevemente lo que se realizó..."></textarea>
</div>

<hr>
<h6>Firmas y conformidad</h6>
<div class="row mb-3">

  {{-- ✅ Usuario solicitante (editable) --}}
  <div class="col-md-4">
    <label class="form-label">Firma de conformidad (Usuario)</label>
    <input type="text" name="firma_usuario" class="form-control"
           placeholder="Nombre del solicitante o usuario" required>
  </div>

  {{-- ✅ Técnico / quien realiza el servicio (automático) --}}
  <div class="col-md-4">
    <label class="form-label">Realiza el servicio (Técnico)</label>
    <input type="text" name="firma_tecnico" class="form-control"
           value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}" readonly>
    <small class="text-muted">
    
      Puesto: {{ Auth::user()->usuario->puesto ?? Auth::user()->role->nombre ?? 'Técnico' }}
    </small>
  </div>

  {{-- ✅ Jefe de área (automático desde base) --}}
  <div class="col-md-4">
    <label class="form-label">Jefe de Área</label>
    <input type="text" name="firma_jefe_area" class="form-control"
value="{{ \App\Models\Usuario::where('puesto', 'Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}"
           readonly>
  </div>
</div>

<div class="mb-3">
  <label class="form-label">Observaciones</label>
  <textarea name="observaciones" class="form-control" rows="2"></textarea>
</div>
        <div class="text-end">
          <button class="btn btn-success"><i class="fas fa-save me-1"></i>Guardar</button>
          <a href="{{ route('admin.formatos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
