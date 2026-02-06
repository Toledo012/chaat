<h2>Se te ha asignado un ticket</h2>

<p><strong>Folio:</strong> {{ $ticket->folio }}</p>
<p><strong>Título:</strong> {{ $ticket->titulo }}</p>
<p><strong>Solicitante:</strong> {{ $ticket->solicitante }}</p>
<p><strong>Prioridad:</strong> {{ ucfirst($ticket->prioridad) }}</p>
<p><strong>Formato requerido:</strong> {{ strtoupper($ticket->tipo_formato) }}</p>

@if($ticket->descripcion)
<p><strong>Descripción:</strong><br>{{ $ticket->descripcion }}</p>
@endif

<p>Ingresa al sistema para atender el ticket.</p>
