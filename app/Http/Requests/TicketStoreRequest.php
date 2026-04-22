<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TicketStoreRequest',
    required: ['name', 'email', 'phone', 'subject'],
    properties: [
        new OA\Property(
            property: 'name',
            type: 'string',
            maxLength: 255,
            example: 'Иван Иванов'
        ),
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            maxLength: 255,
            example: 'test@example.com'
        ),
        new OA\Property(
            property: 'phone',
            type: 'string',
            maxLength: 20,
            example: '+79991234567'
        ),
        new OA\Property(
            property: 'subject',
            type: 'string',
            maxLength: 255,
            example: 'Проблема с заказом'
        ),
        new OA\Property(
            property: 'text',
            type: 'string',
            maxLength: 2000,
            example: 'Добрый день! Не прошёл заказ. Что делать?',
            nullable: true
        )
    ],
    type: 'object'
)]
class TicketStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'text' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Имя обязательно',
            'name.max' => 'Имя не должно превышать 255 символов',
            'email.required' => 'E-mail обязателен',
            'email.email' => 'Некорректный формат E-mail',
            'email.max' => 'E-mail не должен превышать 255 символов',
            'phone.required' => 'Телефон обязателен',
            'phone.max' => 'Телефон не должен превышать 20 символов',
            'subject.required' => 'Тема обращения обязательна',
            'subject.max' => 'Тема не должна превышать 255 символов',
            'text.max' => 'Описание не должно превышать 2000 символов'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => trim($this->email),
            'phone' => trim($this->phone),
            'subject' => trim($this->subject),
            'text' => $this->text
                ? trim($this->text)
                : null,
        ]);
    }
}
