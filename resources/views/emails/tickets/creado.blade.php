<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 1px solid #e0e0e0; }
        .header { background-color: #399e91; padding: 30px; text-align: center; color: #ffffff; }
        .header img { max-width: 180px; height: auto; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 22px; letter-spacing: 1px; text-transform: uppercase; }
        .content { padding: 30px; color: #444444; line-height: 1.6; }
        .ticket-info { background-color: #f0fdfa; border: 1px solid #ccf2ed; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .label { font-weight: 800; color: #2c7a70; text-transform: uppercase; font-size: 11px; display: block; margin-bottom: 2px; }
        .value { color: #333333; font-size: 15px; display: block; margin-bottom: 12px; }
        .priority-badge { display: inline-block; padding: 2px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .prio-alta { background-color: #fee2e2; color: #dc3545; }
        .prio-media { background-color: #fef3c7; color: #d97706; }
        .prio-baja { background-color: #d1fae5; color: #059669; }
        .btn-container { text-align: center; margin-top: 30px; }
        .btn { background-color: #399e91; color: #ffffff !important; padding: 12px 35px; text-decoration: none; border-radius: 50px; font-weight: bold; display: inline-block; }
        .footer { background-color: #f1f1f1; padding: 20px; text-align: center; color: #888888; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- Incrustamos el logo usando public_path para asegurar su visualización --}}
            <img src="{{ $message->embed(public_path('images/logo_semahn2.png')) }}" alt="Logo SEMAHN">
            <h2>Nuevo Ticket Registrado</h2>
        </div>

        <div class="content">
            <p>Se ha generado una nueva solicitud de servicio en el sistema. A continuación se presentan los detalles para su revisión y asignación:</p>

            <div class="ticket-info">
                <span class="label">Folio de Seguimiento</span>
                <span class="value" style="font-weight: bold; color: #399e91; font-size: 18px;">#{{ $ticket->folio }}</span>

                <span class="label">Asunto / Título</span>
                <span class="value">{{ $ticket->titulo }}</span>

                <span class="label">Usuario Solicitante</span>
                <span class="value">{{ $ticket->solicitante }}</span>

                <span class="label">Prioridad Asignada</span>
                <div class="value">
                    <span class="priority-badge {{ 'prio-'.$ticket->prioridad }}">
                        {{ ucfirst($ticket->prioridad) }}
                    </span>
                </div>

                <span class="label">Tipo de Formato</span>
                <span class="value" style="background: #edf2f7; padding: 2px 8px; border-radius: 4px; display: inline-block;">
                    {{ strtoupper($ticket->tipo_formato) }}
                </span>

                @if($ticket->descripcion)
                <div style="margin-top: 10px; border-top: 1px solid #e2e8f0; pt-3;">
                    <span class="label" style="margin-top: 10px;">Descripción del Problema</span>
                    <div style="background: #ffffff; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 14px; color: #555;">
                        {{ $ticket->descripcion }}
                    </div>
                </div>
                @endif
            </div>

            <div class="btn-container">
                <a href="http://4.155.249.140/" class="btn">Ingresar al Sistema</a>
            </div>
        </div>

        <div class="footer">
            <p><strong>Secretaría de Medio Ambiente e Historia Natural</strong></p>
            <p>Sistema de Formatos Digitales - 2026</p>
        </div>
    </div>
</body>
</html>