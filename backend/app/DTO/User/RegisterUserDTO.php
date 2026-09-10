<?php

namespace App\DTO\User;

use Illuminate\Support\Str;

class RegisterUserDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            id: Str::uuid(),
            name: trim(ucfirst($data['name'])),
            email: strtolower(trim($data['email'])),
            password: $data['password']);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
