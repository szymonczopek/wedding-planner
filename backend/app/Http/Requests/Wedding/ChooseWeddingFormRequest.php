<?php

namespace App\Http\Requests\Wedding;

use App\Http\Requests\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class ChooseWeddingFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'wedding_id' => 'required|string|exists:weddings,id',
        ];
    }
}
