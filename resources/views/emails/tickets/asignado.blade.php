<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 10px; overflow: hidden; border: 1px solid #e0e0e0; }
        .header { background: #399e91; padding: 30px; text-align: center; color: #ffffff; }
        .header img { max-width: 160px; margin-bottom: 15px; }
        .content { padding: 30px; color: #444; line-height: 1.6; }
        .ticket-box { background: #f8fbff; border: 1px solid #d1e3f8; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .label { font-weight: bold; font-size: 12px; color: #2c7a70; text-transform: uppercase; margin-top: 10px; }
        .value { font-size: 15px; margin-bottom: 10px; }
        .btn { display: inline-block; margin-top: 25px; padding: 12px 30px; background: #399e91; color: #ffffff !important; text-decoration: none; border-radius: 50px; font-weight: bold; }
        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <img src="https://www.semahn.chiapas.gob.mx/portal/logo/logo_semahn.png" alt="SEMAHN">
        <h2>Ticket Asignado</h2>
    </div>

    <div class="content">
        <p>Se te ha asignado el siguiente ticket para su atención:</p>

        <div class="ticket-box">

            <div class="label">Folio</div>
            <div class="value"><strong>#{{ $ticket->folio }}</strong></div>

            <div class="label">Título</div>
            <div class="value">{{ $ticket->titulo }}</div>

            <div class="label">Solicitante</div>
            <div class="value">{{ $ticket->solicitante }}</div>

            <div class="label">Prioridad</div>
            <div class="value">{{ ucfirst($ticket->prioridad) }}</div>

            <div class="label">Formato</div>
            <div class="value">TIPO {{ strtoupper($ticket->tipo_formato) }}</div>

        </div>

        <center>
            <a href="http://4.155.249.140/" class="btn">Atender Ticket</a>
        </center>

    </div>

    <div class="footer">
        Sistema de Formatos Digitales - SEMAHN 2026 <br>
        Secretaría de Medio Ambiente e Historia Natural
    </div>

</div>
</body>
</html>
