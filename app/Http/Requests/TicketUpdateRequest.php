<?php

namespace App\Http\Requests;

use App\Enums\Ticket\StatusEnum;
use App\Enums\User\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can(PermissionEnum::CHANGE_TICKET_STATUS->value);
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
