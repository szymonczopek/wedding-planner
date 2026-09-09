<?php

namespace App\DTO\User;

class RegisterUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: trim($data['name']),
            email: strtolower(trim($data['email'])),
            password: $data['password']);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
