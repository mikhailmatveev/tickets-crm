<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_id' => 'required|integer|gt:0|exists:roles,id'
        ];
    }

    public function messages(): array
    {
        return [
            'role_id.required' => 'Поле role_id является обязательным',
            'role_id.integer' => 'Поле role_id является целым числом',
            'role_id.gt' => 'Поле role_id должно быть больше 0',
            'role_id.exists' => 'Поля role_id с таким значением не существует'
        ];
    }
}
