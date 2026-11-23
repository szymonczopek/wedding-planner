<?php

namespace App\Http\Requests\Guest;

use App\Enums\AcquaintanceTypesEnum;
use App\Enums\DietsEnum;
use App\Http\Requests\FormRequest;
use App\Models\Wedding;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateGuestFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $guestId = $this->route('guest')?->id;

        return [
            'address_mode' => ['sometimes', Rule::in(['existing', 'new', 'edit'])],
            'address_id' => [
                'nullable',
                'string',
                'exists:App\Models\Address,id',
                Rule::requiredIf(fn () => $this->input('address_mode') === 'existing'),
            ],
            'address' => ['sometimes', 'array'],
            'address.street' => ['nullable', 'string'],
            'address.house_number' => ['required_with:address', 'string'],
            'address.local_number' => ['nullable', 'string'],
            'address.city' => ['required_with:address', 'string'],
            'address.district' => ['nullable', 'string'],
            'address.postal_code' => ['nullable', 'string'],
            'address.country' => ['nullable', 'string'],

            'name' => ['sometimes', 'string'],
            'surname' => ['sometimes', 'string'],
            'acquaintance_type' => ['sometimes', new Enum(AcquaintanceTypesEnum::class)],
            'child' => ['sometimes', 'boolean'],
            'groom_guest' => ['sometimes', 'nullable', 'boolean'],
            'diet' => ['sometimes', new Enum(DietsEnum::class)],
            'main_guest_id' => array_filter([
                'sometimes',
                'nullable',
                Rule::exists('guests', 'id')
                    ->where('wedding_id', Wedding::selectedWedding())
                    ->whereNull('deleted_at'),
                $guestId ? Rule::notIn([$guestId]) : null,
            ]),
        ];
    }
}
