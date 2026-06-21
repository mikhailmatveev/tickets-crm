<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $verificationUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->email,
            subject: 'Ваш аккаунт создан'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.user.created'
        );
    }
}
