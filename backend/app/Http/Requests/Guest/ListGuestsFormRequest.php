<?php

namespace App\Http\Requests\Guest;

use App\Http\Requests\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class ListGuestsFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first' => ['nullable', 'integer', 'min:0'],
            'rowsPerPage' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
