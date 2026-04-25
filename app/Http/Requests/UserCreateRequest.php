<?php

namespace App\Http\Requests;

use App\Enums\User\Permission;
use App\Enums\User\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserCreateRequest',
    required: ['name', 'email', 'password', 'role'],
    properties: [
        new OA\Property(
            property: 'name',
            description: 'Имя пользователя',
            type: 'string',
            maxLength: 255,
            example: 'Иван Иванов'
        ),
        new OA\Property(
            property: 'email',
            description: 'Email пользователя (уникальный)',
            type: 'string',
            format: 'email',
            maxLength: 255,
            example: 'ivan@example.com'
        ),
        new OA\Property(
            property: 'password',
            description: 'Пароль пользователя',
            type: 'string',
            minLength: 6,
            example: 'secret123'
        ),
        new OA\Property(
            property: 'role',
            ref: '#/components/schemas/Role',
            description: 'Роль пользователя'
        )
    ]
)]
class UserCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        $user = auth()->user();
        return $user?->hasRole(Role::ADMIN) && $user?->can(Permission::CREATE_USER);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(
                    'users',
                    'email'
                )
                    ->whereNull('deleted_at')
            ],
            'password' => 'required|string|min:6',
            'role' => [
                'required',
                'string',
                Rule::in(Role::collection())
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Имя" обязательно для заполнения',
            'name.string' => 'Поле "Имя" должно быть строкой',
            'name.max' => 'Поле "Имя" не должно превышать 255 символов',

            'email.required' => 'Поле "E-mail" обязательно для заполнения',
            'email.string' => 'Поле "E-mail" должно быть строкой',
            'email.email' => 'Укажите корректный E-mail',
            'email.max' => 'Поле "E-mail" не должно превышать 255 символов',
            'email.unique' => 'Пользователь с таким E-mail уже существует',

            'password.required' => 'Поле "Пароль" обязательно для заполнения',
            'password.string' => 'Поле "Пароль" должно быть строкой',
            'password.min' => 'Пароль должен содержать не менее 6 символов',

            'role.required' => 'Поле "Роль" обязательно для заполнения',
            'role.string' => 'Поле "Роль" должно быть строкой',
            'role.in' => 'Выбрана недопустимая роль'
        ];
    }
}
