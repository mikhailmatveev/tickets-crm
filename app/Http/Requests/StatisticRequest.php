<?php

namespace App\Http\Requests;

use App\Enums\Ticket\Period;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'StatisticRequest',
    properties: [
        new OA\Property(
            property: 'period',
            ref: '#/components/schemas/Period',
            type: 'string',
            nullable: true
        )
    ],
    type: 'object'
)]
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
