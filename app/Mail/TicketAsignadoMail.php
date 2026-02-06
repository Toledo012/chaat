<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketAsignadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function build()
    {
        return $this->subject('Ticket asignado: '.$this->ticket->folio)
            ->view('emails.tickets.asignado');
    }
}
