<?php

namespace App\Http\Requests;

use App\Enums\Ticket\StatusEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Throwable;

class TicketFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'sometimes|string|min:3|max:255',
            'phone' => 'sometimes|string|min:3|max:20',
            'date' => 'sometimes|date',
            'status' => [
                'sometimes',
                'string',
                Rule::in(StatusEnum::collection())
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'email.string' => 'Поле email должно быть строкой',
            'email.min' => 'Введите минимум 3 символа для поиска по email',
            'email.max' => 'Поле email не должно превышать 255 символов',

            'phone.string' => 'Поле phone должно быть строкой',
            'phone.min' => 'Введите минимум 3 символа для поиска по телефону',
            'phone.max' => 'Поле phone не должно превышать 20 символов',

            'date.date' => 'Введите корректную дату',

            'status.string' => 'Поле status должно быть строкой',
            'status.in' => 'Недопустимое значение статуса'
        ];
    }

    /**
     * Получение статуса из реквеста
     * @return ?StatusEnum
     */
    public function status(): ?StatusEnum
    {
        return StatusEnum::tryFrom($this->input('status'));
    }

    /**
     * Дополнительная обработка поля с датой с использованием Carbon
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('filters') && is_array($this->filters)) {
            $this->merge($this->filters);
        }

        if ($this->filled('date')) {
            try {
                $this->merge([
                    'date' => Carbon::parse($this->date)->toDateString(),
                ]);
            } catch (Throwable) {
                // Оставляем исходное значение, чтобы rule 'date' отработал и вернул 422
            }
        }
    }
}
