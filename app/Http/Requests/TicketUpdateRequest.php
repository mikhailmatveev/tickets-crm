<?php

namespace App\Http\Requests;

use App\Enums\Ticket\StatusEnum;
use App\Enums\User\PermissionEnum;
use App\Enums\User\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        $user = auth()->user();
        // Временно сделал такую проверку, чтобы не падало в 403 ошибку
        return ($user?->hasRole(RoleEnum::ADMIN) || $user?->hasRole(RoleEnum::MANAGER))
            ?? false
        ;
        // TODO: Добавить проверку прав (permissions), если пользователь был добавлен через интерфейс
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(StatusEnum::collection())
            ],
            'replyText' => [
                'required_if:status,done',
                'prohibited_unless:status,done',
                'string',
                'max:2000'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Поле status является обязательным',
            'status.string' => 'Поле status должно быть строкой',
            'status.in' => 'Поле status может принимать только одно из значений new, working или done',
            'replyText.required_if' => 'Поле ответа обязательно при завершении тикета',
            'replyText.string' => 'Поле replyText должно быть строкой',
            'replyText.max' => 'Поле replyText не должно превышать 2000 символов'
        ];
    }
}
