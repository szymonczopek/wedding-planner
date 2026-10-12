<?php

namespace App\Http\Requests;

use App\Enums\UserRolesEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class WeddingFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'date' => 'date|nullable',
            'role' => ['required', new Enum(UserRolesEnum::class)],
        ];
    }
}
