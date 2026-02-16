<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e0e0e0; }
        .header { background-color: #399e91; padding: 30px; text-align: center; color: #ffffff; }
        .header img { max-width: 150px; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 22px; letter-spacing: 1px; text-transform: uppercase; }
        .content { padding: 30px; color: #444444; line-height: 1.6; }
        .ticket-info { background-color: #f8fbff; border: 1px solid #d1e3f8; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .label { font-weight: 800; color: #2c7a70; text-transform: uppercase; font-size: 11px; display: block; margin-bottom: 2px; }
        .value { color: #333333; font-size: 15px; margin-bottom: 10px; display: block; }
        .priority-alta { color: #dc3545; font-weight: bold; }
        .btn-container { text-align: center; margin-top: 30px; }
        .btn { background-color: #399e91; color: #ffffff !important; padding: 12px 35px; text-decoration: none; border-radius: 50px; font-weight: bold; display: inline-block; }
        .footer { background-color: #f1f1f1; padding: 20px; text-align: center; color: #888888; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo SEMAHN">
        <h2>Nuevo Ticket Registrado</h2>
    </div>

    <div class="content">
        <p>Se ha generado una nueva solicitud de servicio en el sistema.</p>

        <div class="ticket-info">

            <span class="label">Folio</span>
            <span class="value" style="font-weight: bold; color: #399e91;">
                    #{{ $ticket->folio }}
                </span>

            <span class="label">Título</span>
            <span class="value">{{ $ticket->titulo }}</span>

            <span class="label">Solicitante</span>
            <span class="value">{{ $ticket->solicitante }}</span>

            <span class="label">Prioridad</span>
            <span class="value {{ $ticket->prioridad === 'alta' ? 'priority-alta' : '' }}">
                    {{ ucfirst($ticket->prioridad) }}
                </span>

            <span class="label">Formato</span>
            <span class="value">TIPO {{ strtoupper($ticket->tipo_formato) }}</span>

            @if($ticket->descripcion)
                <span class="label">Descripción</span>
                <div style="background:#ffffff; padding:10px; border-radius:6px; border:1px solid #e2e8f0; font-size:14px;">
                    {{ $ticket->descripcion }}
                </div>
            @endif

        </div>

        <div class="btn-container">
            <a href="http://4.155.249.140/" class="btn">Ingresar al Sistema</a>
        </div>
    </div>

    <div class="footer">
        <p><strong>Sistema de Formatos Digitales - SEMAHN 2026</strong></p>
        <p>Secretaría de Medio Ambiente e Historia Natural</p>
    </div>

</div>
</body>
</html>
