<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Formato D - Mantenimiento / Entrega de Equipos Personales</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container">
  <div class="card shadow border-0">
    <div class="card-header bg-warning text-dark">
      <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Formato D - Entrega / Mantenimiento de Equipos Personales</h5>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('admin.formatos.d.store') }}">
        @csrf

        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Equipo</label>
            <input type="text" name="equipo" class="form-control" placeholder="Ej. Laptop, CPU, Monitor, Impresora" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Marca</label>
            <select name="marca" class="form-select">
              <option value="">Seleccionar marca</option>
              <option value="HP">HP</option>
              <option value="Dell">Dell</option>
              <option value="Lenovo">Lenovo</option>
              <option value="Asus">Asus</option>
              <option value="Acer">Acer</option>
              <option value="Apple">Apple</option>
              <option value="Samsung">Samsung</option>
              <option value="Epson">Epson</option>
              <option value="Brother">Brother</option>
              <option value="Canon">Canon</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Modelo / Año</label>
            <select name="modelo" class="form-select">
              <option value="">Seleccionar año</option>
              @for ($i = now()->year; $i >= now()->year - 10; $i--)
                <option value="{{ $i }}">{{ $i }}</option>
              @endfor
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Número de serie</label>
            <input type="text" name="serie" class="form-control" placeholder="Ej. SN12345XYZ">
          </div>
        </div>


        <hr>
<div class="row mb-3">
  <div class="col-md-4">
    <label class="form-label">Otorgante</label>
    <input type="text" name="otorgante" class="form-control"
           placeholder="Nombre de quien recibe el equipo" required>


  </div>

  <div class="col-md-4">
    <label class="form-label">Receptor</label>
    <input type="text" name="receptor" class="form-control"

               value="{{ Auth::user()->usuario->nombre ?? Auth::user()->name }}" readonly>



  </div>

  <div class="col-md-4">
    <label class="form-label">Jefe de Área</label>
    <input type="text" name="firma_jefe_area" class="form-control"
           value="{{ \App\Models\Usuario::where('puesto','Jefe de Área')->value('nombre') ?? 'Jefe de Área' }}" readonly>
  </div>
</div>

        <div class="mb-3">
          <label class="form-label">Observaciones</label>
          <textarea name="observaciones" class="form-control" rows="2"
                    placeholder="Ej. Se entrega equipo en buenas condiciones, se realizó mantenimiento interno, etc."></textarea>
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
