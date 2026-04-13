<?php

namespace App\Http\Requests;

use App\Enums\Ticket\Period;
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
                    Period::DAY,
                    Period::WEEK,
                    Period::MONTH
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
}
