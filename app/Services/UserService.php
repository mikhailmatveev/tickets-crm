<?php

namespace App\Services;

use App\DTO\UserCreateData;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(UserCreateData $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make($data->password),
                'email_verified_at' => Carbon::now()
            ]);

            $user->syncRoles($data->role);
            // TODO: Добавить проверку прав (permissions), если пользователь был добавлен через интерфейс
            // TODO: Так же добавить подтверждение email по ссылке

            return $user;
        });
    }
}
