<?php

namespace App\Http\Requests;

use App\Enums\Ticket\PeriodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatisticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => [
                'nullable',
                'string',
                Rule::in([
                    PeriodEnum::DAY,
                    PeriodEnum::WEEK,
                    PeriodEnum::MONTH
                ])
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'period.in' => 'Параметр period должен быть day, week или month',
        ];
    }

    /**
     * Вычисляем период из реквеста (по умолчанию - день)
     * @return PeriodEnum
     */
    public function period(): PeriodEnum
    {
        return PeriodEnum::tryFrom($this->input('period')) ?? PeriodEnum::DAY;
    }
}
