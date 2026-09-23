<?php

namespace App\DTO\User;

use App\Models\User;
use Illuminate\Support\Str;

readonly class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $role,
        public string $invite_code,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: trim(ucfirst(strtolower($data['name']))),
            email: strtolower(trim($data['email'])),
            password: $data['password'],
            role: $data['role'],
            invite_code: self::generateInviteCode()
        );
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'invite_code' => $this->invite_code,
        ];
    }

    public static function generateInviteCode(): string
    {
        do {
            $inviteCode = strtolower(Str::random(4).'-'.Str::random(4));
        } while (User::where('invite_code', $inviteCode)->exists());

        return $inviteCode;
    }
}
