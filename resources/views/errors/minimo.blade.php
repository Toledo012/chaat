{{--
    Plantilla de las pantallas de error.
    NO consulta la BD ni la sesión, a propósito: un 500 puede ser justamente la BD caída,
    y una pantalla de error que necesita la BD no se puede pintar. Tampoco muestra
    detalles técnicos: esos quedan en el panel "Errores del sistema".
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo') — SEMAHN</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
               font-family: Arial, Helvetica, sans-serif; background: #f4f7f6; color: #333; }
        .caja { max-width: 460px; padding: 40px 24px; text-align: center; }
        .caja img { height: 56px; margin-bottom: 28px; }
        .codigo { font-size: 64px; font-weight: bold; color: #399e91; margin: 0; }
        h1 { font-size: 20px; margin: 12px 0; }
        p { font-size: 15px; line-height: 1.6; color: #666; }
        .acciones { margin-top: 28px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn { display: inline-block; padding: 10px 26px; border-radius: 50px; font-weight: bold; text-decoration: none;
               border: 2px solid #399e91; }
        .btn-primario { background: #399e91; color: #fff; }
        .btn-secundario { background: transparent; color: #399e91; }
        .pie { margin-top: 48px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="caja">
        <img src="{{ asset('images/logo_semahn2.png') }}" alt="SEMAHN">
        <p class="codigo">@yield('codigo')</p>
        <h1>@yield('titulo')</h1>
        <p>@yield('mensaje')</p>

        <div class="acciones">
            <a href="javascript:history.back()" class="btn btn-secundario">Regresar</a>
            <a href="{{ url('/') }}" class="btn btn-primario">Ir al inicio</a>
        </div>

        <p class="pie">Sistema de Formatos Digitales<br>Secretaría de Medio Ambiente e Historia Natural</p>
    </div>
</body>
</html>
