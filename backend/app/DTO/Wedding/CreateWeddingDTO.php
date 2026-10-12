<?php

namespace App\DTO\Wedding;

use App\Enums\UserRolesEnum;
use App\Models\Wedding;
use Illuminate\Support\Str;

readonly class CreateWeddingDTO
{
    public function __construct(
        public string $name,
        public ?string $date,
        public UserRolesEnum $role,
        public bool $is_active,
        public string $invite_code,
    ) {}

    /**
     * @param  array<string, bool|string|null>  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            date: $data['date'] ?? null,
            role: UserRolesEnum::from($data['role']),
            is_active: true,
            invite_code: self::generateInviteCode(),
        );
    }

    /**
     * @return array<string, bool|string|null>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'date' => $this->date,
            'role' => $this->role->value,
            'is_active' => $this->is_active,
            'invite_code' => $this->invite_code,
        ];
    }

    public static function generateInviteCode(): string
    {
        do {
            $inviteCode = strtolower(Str::random(4).'-'.Str::random(4));
        } while (Wedding::where('invite_code', $inviteCode)->exists());

        return $inviteCode;
    }
}
