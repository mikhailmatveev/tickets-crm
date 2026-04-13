<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class WidgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_id' => ['nullable', 'integer', 'gt:0']
        ];
    }

    public function messages(): array
    {
        return [
            'ticket_id.integer' => 'ticket_id является целым числом',
            'ticket_id.gt' => 'ticket_id должно быть больше 0'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()
                ->route('widget.error')
                ->withErrors($validator)
                ->withInput()
        );
    }
}
