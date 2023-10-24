<?php

namespace App\Mail;

use App\Models\SentEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;

    public string $body;

    public string $sender;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $emailSubject, string $body, string $sender)
    {
        $this->emailSubject = $emailSubject;
        $this->body = $body;
        $this->sender = $sender;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address($this->sender),
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.content',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
