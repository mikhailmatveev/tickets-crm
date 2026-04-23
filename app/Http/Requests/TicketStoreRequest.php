<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TicketStoreRequest',
    required: ['name', 'email', 'phone', 'subject', 'text'],
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
            example: 'Добрый день! Не прошёл заказ. Что делать?'
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
            'phone' => 'required|string|regex:/^\+[1-9]\d{7,14}$/',
            'subject' => 'required|string|max:255',
            'text' => 'required|string|max:2000',
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
            'phone.regex' => 'Телефон должен быть в формате E.164 (только + и цифры)',
            'subject.required' => 'Тема обращения обязательна',
            'subject.max' => 'Тема не должна превышать 255 символов',
            'text.required' => 'Описание обязательно',
            'text.max' => 'Описание не должно превышать 2000 символов'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => trim($this->email),
            'phone' => trim($this->phone),
            'subject' => trim($this->subject),
            'text' => trim($this->text)
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Данные не прошли валидацию',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
