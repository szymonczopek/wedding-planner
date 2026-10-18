<?php

namespace App\Http\Requests\Wedding;

use App\Enums\UserRolesEnum;
use App\Http\Requests\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class CreateWeddingFormRequest extends FormRequest
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
