<?php

namespace App\Http\Requests\Wedding;

use App\Enums\UserRolesEnum;
use App\Http\Requests\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class JoinWeddingFormRequest extends FormRequest
{
    public function messages(): array
    {
        return [
            'invite_code.exists' => 'Nie istnieje taki kod wesela',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invite_code' => 'required|string|exists:weddings,invite_code',
            'role' => ['required', new Enum(UserRolesEnum::class)],
        ];
    }
}
