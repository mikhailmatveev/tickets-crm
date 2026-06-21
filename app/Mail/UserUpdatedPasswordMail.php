<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserUpdatedPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->email,
            subject: 'Пароль вашей учётной записи изменён'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.user.password.updated'
        );
    }
}
