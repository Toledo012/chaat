<h2>Tu solicitud fue completada</h2>

<p><strong>Folio:</strong> {{ $ticket->folio }}</p>
<p><strong>Título:</strong> {{ $ticket->titulo }}</p>
<p><strong>Solicitante:</strong> {{ $ticket->solicitante }}</p>
<p><strong>Estado:</strong> {{ ucfirst(str_replace('_',' ',$ticket->estado)) }}</p>

@if($ticket->id_servicio)
<p><strong>ID Servicio:</strong> {{ $ticket->id_servicio }}</p>
@endif

<p>Ya puedes entrar al sistema para ver el detalle y descargar el PDF del formato.</p>
