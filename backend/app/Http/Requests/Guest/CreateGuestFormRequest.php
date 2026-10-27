<?php

namespace App\Http\Requests\Guest;

use App\Enums\AcquaintanceTypesEnum;
use App\Enums\DietsEnum;
use App\Http\Requests\FormRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class CreateGuestFormRequest extends FormRequest
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
        $weddingId = $this->input('wedding_id');
        $weddingId = is_string($weddingId) ? $weddingId : null;

        return [
            'address_id' => ['sometimes', 'string', 'exists:App\Models\Address,id'],
            'address' => ['required_without:address_id', 'array'],
            'address.street' => ['nullable', 'string'],
            'address.house_number' => ['required_with:address', 'string', 'required'],
            'address.local_number' => ['nullable', 'string'],
            'address.city' => ['required_with:address', 'string', 'required'],
            'address.district' => ['nullable', 'string'],
            'address.postal_code' => ['nullable', 'string'],
            'address.country' => ['nullable', 'string'],

            'name' => ['required', 'string'],
            'surname' => ['required', 'string'],
            'acquaintance_type' => ['required', new Enum(AcquaintanceTypesEnum::class)],
            'child' => ['required', 'boolean'],
            'groom_guest' => ['nullable', 'boolean'],
            'diet' => ['required', new Enum(DietsEnum::class)],
            'wedding_id' => [
                'required',
                'string',
                Rule::exists('weddings', 'id')->whereNull('deleted_at'),
            ],
            'main_guest_id' => [
                'sometimes',
                Rule::exists('guests', 'id')
                    ->where('wedding_id', $weddingId)
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
