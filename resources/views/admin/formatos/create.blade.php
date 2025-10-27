
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo Formato</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
  <div class="container">
    <div class="card shadow-lg border-0 rounded-3">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i>Crear nuevo Formato</h4>
      </div>
      <div class="card-body">
        <p>Selecciona el tipo de formato que deseas llenar:</p>
        <div class="row g-3">
          <div class="col-md-3">
            <a href="{{ route('admin.formatos.a') }}" class="btn btn-success w-100 p-4">
              <i class="fas fa-laptop me-2"></i>Formato A<br><small>Desarrollo / Soporte</small>
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('admin.formatos.b') }}" class="btn btn-info w-100 p-4">
              <i class="fas fa-desktop me-2"></i>Formato B<br><small>Equipos / Impresoras</small>
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('admin.formatos.c') }}" class="btn btn-warning w-100 p-4">
              <i class="fas fa-network-wired me-2"></i>Formato C<br><small>Redes / Telefonía</small>
            </a>
          </div>
          <div class="col-md-3">
            <a href="{{ route('admin.formatos.d') }}" class="btn btn-secondary w-100 p-4">
              <i class="fas fa-handshake me-2"></i>Formato D<br><small>Préstamo de Equipo</small>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
