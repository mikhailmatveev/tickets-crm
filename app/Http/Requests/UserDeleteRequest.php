<?php

namespace App\Http\Requests;

use App\Enums\User\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;

class UserDeleteRequest extends FormRequest
{
    /**
     * Удалять пользователей может только админ
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->user()->can(PermissionEnum::DELETE_USER->value);
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|gt:0|exists:users,id'
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Поле id является обязательным',
            'id.integer' => 'Поле id должно быть целым числом',
            'id.gt' => 'Поле id должно быть больше 0',
            'id.exists' => 'Пользователь с таким id не найден'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}
