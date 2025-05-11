<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class Ticket extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected $data)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('alvenorlando@gmail.com', 'FESTIX'),
            replyTo: [
                new Address('alvenorlando@gmail.com', 'FESTIX'),
            ],
            subject: 'Ticket [FESTIX]',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'tickets.ticket',
            with: [
                // 'date' => $this->data['date'],
                // 'time' => $this->data['time'],
                // 'location' => $this->data['location'],
                // 'image' => $this->data['image'],
                // 'title' => $this->data['title'],
                // 'category' => $this->data['category'],
                // 'seat' => $this->data['seat'],
                // 'code' => $this->data['code'],
                // 'qrcode' => $this->data['qrcode'],

                'tickets' => $this->data,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
