<?php

namespace App\Modules\Lead\Domain\ValueObjects;

final class LeadDataField
{
    public function __construct(
        private readonly string $name,
        private readonly string $value,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function fromArray(array $data): LeadDataField
    {
        return new self($data['name'] ?? '', $data['value'] ?? '');
    }
}
