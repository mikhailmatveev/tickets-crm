<?php

namespace App\Http\Requests;

use App\Enums\User\Permission;
use App\Enums\User\Role;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserUpdateRoleRequest',
    required: ['role_id'],
    properties: [
        new OA\Property(
            property: 'role_id',
            description: 'ID роли из таблицы roles',
            type: 'integer',
            minimum: 1,
            example: 2
        )
    ]
)]
class UserUpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        $user = auth()->user();
        return $user?->hasRole(Role::ADMIN) && $user?->can(Permission::CHANGE_USER_ROLE);
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
