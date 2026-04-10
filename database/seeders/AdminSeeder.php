<?php

namespace Database\Seeders;

use App\Enums\User\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('admin.name');
        $email = config('admin.email');
        $password = config('admin.password');

        if (app()->environment('production') && !$password) {
            throw new \RuntimeException('Пароль администратора не должен быть пустым!');
        }

        // Проверим, что пользователь ещё не создан
        $userExists = User::query()
            ->where(['email' => $email])
            ->exists()
        ;

        if ($userExists) {
            $this->command->info('Администратор уже существует');
            return;
        }

        // Создадим администратора
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->email_verified_at = now();
        $user->password = Hash::make($password);
        $user->remember_token = Str::random(10);

        if ($user->save()) {
            // Зададим роль
            $user->assignRole(Role::ADMIN);
            $this->command->info('Администратор создан успешно');
        } else {
            $this->command->error('Произошла ошибка при создании администратора');
        }
    }
}
