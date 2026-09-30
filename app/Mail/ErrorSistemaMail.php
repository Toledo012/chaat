<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorSistemaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public object $error) {}

    public function build()
    {
        return $this->subject('Falla en el sistema: ' . class_basename($this->error->clase))
            ->view('emails.errores.aviso');
    }
}
