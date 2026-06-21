<?php

namespace App\Notifications;

use App\Mail\UserUpdatedPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserUpdatedPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $password
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
     * @return UserUpdatedPasswordMail
     */
    public function toMail(object $notifiable): UserUpdatedPasswordMail
    {
        return new UserUpdatedPasswordMail(
            name: $notifiable->name,
            email: $notifiable->email,
            password: $this->password
        );
    }
}
