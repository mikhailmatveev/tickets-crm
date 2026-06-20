<?php

namespace App\Services;

use App\DTO\UserCreateData;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Throwable;

class UserService
{
    const int EMAIL_VERIFICATION_EXPIRE_DEFAULT = 60;

    /**
     * Создает пользователя, назначает ему роль и отправляет уведомление по E-Mail для подтверждения авторизации
     * @param UserCreateData $data DTO с данными пользователя
     * @return User Модель пользователя
     * @throws Throwable
     */
    public function create(UserCreateData $data): User
    {
        // Создаём пользователя и присваиваем ему роль
        $user = $this->createUser($data);

        // Отправляем письмо со ссылкой на верификацию
        $this->sendEmailVerificationNotification($user, $data->password);

        return $user;
    }

    /**
     * Отправляет уведомление по E-Mail для подтверждения авторизации
     * @param User $user Модель пользователя
     * @param string $password Оригинальный пароль (в открытом виде в письме)
     * @return void
     */
    public function sendEmailVerificationNotification(User $user, string $password = '********'): void
    {
        // Генерируем подписанную ссылку для верификации email
        $verificationUrl = URL::temporarySignedRoute(
            name: 'verification.verify',
            expiration: now()->addMinutes(self::EMAIL_VERIFICATION_EXPIRE_DEFAULT),
            parameters: [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ]
        );

        // Создаёт класс уведомления с данными и отправляет их в очередь
        $user->notify(new UserCreatedNotification(
            password: $password,
            verificationUrl: $verificationUrl
        ));
    }

    /**
     * Транзакционный метод создания пользователя с присвоением роли
     * @param UserCreateData $data DTO с данными пользователя
     * @return User Модель пользователя
     * @throws Throwable
     */
    protected function createUser(UserCreateData $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make($data->password)
            ]);

            $user->syncRoles($data->role);

            return $user;
        });
    }
}
