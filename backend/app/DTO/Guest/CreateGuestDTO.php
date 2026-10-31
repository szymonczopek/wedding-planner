<?php

namespace App\DTO\Guest;

use App\Enums\AcquaintanceTypesEnum;
use App\Enums\DietsEnum;

class CreateGuestDTO
{
    public function __construct(
        public string $name,
        public string $surname,
        public AcquaintanceTypesEnum $acquaintance_type,
        public bool $child,
        public ?bool $groom_guest,
        public bool $invitation_prepared,
        public bool $invitation_delivered,
        public bool $confirmed_attendance,
        public DietsEnum $diet,
        public string $wedding_id,
        public string $created_by,
        public ?string $main_guest_id,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            surname: $data['surname'],
            acquaintance_type: AcquaintanceTypesEnum::from($data['acquaintance_type']),
            child: (bool) $data['child'],
            groom_guest: isset($data['groom_guest']) ? (bool) $data['groom_guest'] : null,
            invitation_prepared: false,
            invitation_delivered: false,
            confirmed_attendance: false,
            diet: DietsEnum::from($data['diet']),
            wedding_id: auth()->user()->selectedWeddingId(),
            created_by: auth()->user()->id,
            main_guest_id: $data['main_guest_id'] ?? null,
        );
    }

    /**
     * @return array<string, bool|float|string|null>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'surname' => $this->surname,
            'acquaintance_type' => $this->acquaintance_type->value,
            'child' => $this->child,
            'groom_guest' => $this->groom_guest,
            'invitation_prepared' => $this->invitation_prepared,
            'invitation_delivered' => $this->invitation_delivered,
            'confirmed_attendance' => $this->confirmed_attendance,
            'diet' => $this->diet->value,
            'wedding_id' => $this->wedding_id,
            'created_by' => $this->created_by,
            'main_guest_id' => $this->main_guest_id,
        ];
    }
}
