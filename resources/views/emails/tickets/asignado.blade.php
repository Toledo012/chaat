<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 1px solid #e0e0e0; }
        .header { background-color: #399e91; padding: 30px; text-align: center; color: #ffffff; }
        .header img { max-width: 150px; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 22px; letter-spacing: 1px; text-transform: uppercase; }
        .content { padding: 30px; color: #444444; line-height: 1.6; }
        .ticket-info { background-color: #f8fbff; border: 1px solid #d1e3f8; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .ticket-info p { margin: 8px 0; border-bottom: 1px solid #edf2f7; padding-bottom: 5px; }
        .ticket-info p:last-child { border-bottom: none; }
        .label { font-weight: 800; color: #2c7a70; text-transform: uppercase; font-size: 11px; display: block; margin-bottom: 2px; }
        .value { color: #333333; font-size: 15px; }
        .priority-alta { color: #dc3545; fw-bold; }
        .btn-container { text-align: center; margin-top: 30px; }
        .btn { background-color: #399e91; color: #ffffff !important; padding: 12px 35px; text-decoration: none; border-radius: 50px; font-weight: bold; display: inline-block; box-shadow: 0 4px 6px rgba(57, 158, 145, 0.3); }
        .footer { background-color: #f1f1f1; padding: 20px; text-align: center; color: #888888; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        {{-- ENCABEZADO CON LOGO --}}
        <div class="header">
            <img src="{{ asset('images/logo_semahn2.png') }}" alt="Logo SEMAHN">

            <h2>Asignación de Ticket</h2>
        </div>

        <div class="content">
            <p>Hola, se te informa que se ha registrado una nueva asignación en el sistema bajo tu responsabilidad. A continuación, los detalles del ticket:</p>

            {{-- DETALLES DEL TICKET --}}
            <div class="ticket-info">
                <p>
                    <span class="label">Folio de Referencia</span>
                    <span class="value" style="font-weight: bold; color: #399e91;">#{{ $ticket->folio }}</span>
                </p>
                <p>
                    <span class="label">Asunto / Título</span>
                    <span class="value">{{ $ticket->titulo }}</span>
                </p>
                <p>
                    <span class="label">Nombre del Solicitante</span>
                    <span class="value">{{ $ticket->solicitante }}</span>
                </p>
                <p>
                    <span class="label">Nivel de Prioridad</span>
                    <span class="value {{ $ticket->prioridad === 'alta' ? 'priority-alta' : '' }}">
                        {{ ucfirst($ticket->prioridad) }}
                    </span>
                </p>
                <p>
                    <span class="label">Formato Digital Requerido</span>
                    <span class="value" style="background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-size: 13px;">
                        TIPO {{ strtoupper($ticket->tipo_formato) }}
                    </span>
                </p>

                @if($ticket->descripcion)
                <div style="margin-top: 15px;">
                    <span class="label">Descripción Técnica</span>
                    <div style="background: #ffffff; padding: 10px; border-radius: 5px; border: 1px dashed #cbd5e0; font-size: 14px; margin-top: 5px;">
                        {{ $ticket->descripcion }}
                    </div>
                </div>
                @endif
            </div>

            <p style="text-align: center; font-size: 14px; color: #666;">Por favor, ingresa al sistema para iniciar con la atención del requerimiento.</p>

            {{-- BOTÓN DE ACCIÓN --}}
            <div class="btn-container">
                <a href="http://4.155.249.140/" class="btn">Atender Ticket Ahora</a>
            </div>
        </div>

        {{-- PIE DE PÁGINA --}}
        <div class="footer">
            <p>Este es un mensaje generado automáticamente por el <br> <strong>Sistema de Formatos Digitales - SEMAHN 2026</strong></p>
            <p>&copy; Secretaría de Medio Ambiente e Historia Natural</p>
        </div>
    </div>
</body>
</html>