<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{

    use Queueable, SerializesModels;
    public array $dati;

    
    public function __construct(array $dati)
    {
        $this->dati = $dati;
    }

    
    public function envelope(): Envelope
    {
        return new Envelope(
            // L'oggetto della mail viene preso dai dati del form
            subject: '[NBA Universe] ' . $this->dati['oggetto'],
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'mail.contact',
            // I dati sono già accessibili tramite $this->dati
            // perché la proprietà è public (vedi sopra)
        );
    }


    public function attachments(): array
    {
        return [];
    }
}