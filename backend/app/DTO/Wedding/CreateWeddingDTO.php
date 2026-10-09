<?php

namespace App\DTO\Wedding;

readonly class CreateWeddingDTO
{
    public function __construct(
        public string $name,
        public string $created_by,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            created_by: $data['created_by']
        );
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'created_by' => $this->created_by,
        ];
    }
}
