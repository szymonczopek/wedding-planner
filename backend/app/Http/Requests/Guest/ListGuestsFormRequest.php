<?php

namespace App\Http\Requests\Guest;

use App\Http\Requests\FormRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ListGuestsFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $weddingId = $this->input('wedding_id');

        if (! is_string($weddingId) || $weddingId === '') {
            return true;
        }

        $user = Auth::user();

        return $user instanceof User && $user->weddings()->whereKey($weddingId)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'wedding_id' => [
                'required',
                'string',
                Rule::exists('weddings', 'id')->whereNull('deleted_at'),
            ],
        ];
    }
}
