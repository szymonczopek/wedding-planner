<?php

namespace App\Http\Requests\Address;

use App\Http\Requests\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class AutocompleteAddressFormRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q' => ['required', 'string', 'min:2', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'q' => 'szukana fraza',
        ];
    }
}
