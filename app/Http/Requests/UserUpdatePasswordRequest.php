<?php

namespace App\Http\Requests;

use App\Enums\User\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can(PermissionEnum::CHANGE_USER_PASSWORD);
    }
}
