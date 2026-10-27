<?php

namespace App\DTO\Address;

class CreateAddressDTO
{
    public function __construct(
        public ?string $street,
        public string $house_number,
        public ?string $local_number,
        public string $city,
        public ?string $district,
        public ?string $postal_code,
        public ?string $country,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            street: $data['street'] ?? null,
            house_number: $data['house_number'],
            local_number: $data['local_number'] ?? null,
            city: $data['city'],
            district: $data['district'] ?? null,
            postal_code: $data['postal_code'] ?? null,
            country: $data['country'] ?? null,
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'house_number' => $this->house_number,
            'local_number' => $this->local_number,
            'city' => $this->city,
            'district' => $this->district,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
        ];
    }
}
