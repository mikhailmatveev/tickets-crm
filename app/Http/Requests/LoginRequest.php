<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginRequest',
    required: ['email', 'password'],
    properties: [
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            maxLength: 255,
            example: 'ivan.ivanov@example.com'
        ),
        new OA\Property(
            property: 'password',
            type: 'string',
            format: 'password',
            minLength: 6,
            example: 'secret123'
        ),
    ],
    type: 'object'
)]
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|string|max:255',
            'password' => 'required|string|min:6'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Поле email обязательно для заполнения',
            'email.email' => 'Введите корректный адрес электронной почты',
            'email.string' => 'Поле email должно быть строкой',
            'email.max' => 'Поле email не должно превышать 255 символов',
            'password.required' => 'Поле password обязательно для заполнения',
            'password.string' => 'Пароль должен быть строкой',
            'password.min' => 'Пароль должен быть не меньше 8 символов'
        ];
    }
}
