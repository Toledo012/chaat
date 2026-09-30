<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 10px; overflow: hidden; border: 1px solid #e0e0e0; }
        .header { background: #b23b3b; padding: 30px; text-align: center; color: #ffffff; }
        .header img { max-width: 160px; margin-bottom: 15px; }
        .content { padding: 30px; color: #444; line-height: 1.6; }
        .ticket-box { background: #fff8f8; border: 1px solid #f0d1d1; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .label { font-weight: bold; font-size: 12px; color: #8a2d2d; text-transform: uppercase; margin-top: 10px; }
        .value { font-size: 15px; margin-bottom: 10px; word-break: break-word; }
        .btn { display: inline-block; margin-top: 25px; padding: 12px 30px; background: #399e91; color: #ffffff !important; text-decoration: none; border-radius: 50px; font-weight: bold; }
        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <img src="https://www.semahn.chiapas.gob.mx/portal/logo/logo_semahn.png" alt="SEMAHN">
        <h2>Falla en el sistema</h2>
    </div>

    <div class="content">
        <p>Se registró una falla. El detalle completo (traza incluida) está en el panel de errores.</p>

        <div class="ticket-box">
            <div class="label">Error</div>
            <div class="value">{{ class_basename($error->clase) }}: {{ \Illuminate\Support\Str::limit($error->mensaje, 300) }}</div>

            <div class="label">Ubicación</div>
            <div class="value">{{ $error->archivo }}:{{ $error->linea }}</div>

            <div class="label">Petición</div>
            <div class="value">{{ $error->metodo }} {{ $error->url ?? '(consola)' }}</div>

            <div class="label">Ocurrencias</div>
            <div class="value">{{ $error->ocurrencias }} (primera vez: {{ $error->primera_vez }})</div>
        </div>

        <center>
            <a href="{{ rtrim(config('app.sistema_url'), '/') }}/admin/errores/{{ $error->id_error }}" class="btn">Ver en el panel</a>
        </center>
    </div>

    <div class="footer">
        Sistema de Formatos Digitales - SEMAHN 2026 <br>
        Secretaría de Medio Ambiente e Historia Natural
    </div>

</div>
</body>
</html>
