<?php

namespace App\Notifications;

use App\Mail\UserCreatedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $password,
        private readonly string $verificationUrl
    ) {}

    /**
     * Воркер забирает задачу из очереди и отправляет по каналу mail
     * @param object $notifiable
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Вызов Mailable-класса, который знает, какой шаблон и какие данные в нём использовать
     * @param object $notifiable
     * @return UserCreatedMail
     */
    public function toMail(object $notifiable): UserCreatedMail
    {
        return new UserCreatedMail(
            name: $notifiable->name,
            email: $notifiable->email,
            password: $this->password,
            verificationUrl: $this->verificationUrl
        );
    }
}
