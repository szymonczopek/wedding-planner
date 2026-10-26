<?php

namespace App\Http\Requests\Address;

use App\Http\Requests\FormRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SearchAddressesFormRequest extends FormRequest
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
