<?php

namespace App\DTO\User;

readonly class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: trim(ucfirst(strtolower($data['name']))),
            email: strtolower(trim($data['email'])),
            password: $data['password']
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
        ];
    }


}
