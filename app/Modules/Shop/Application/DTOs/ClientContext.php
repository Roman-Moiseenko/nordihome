<?php

namespace App\Modules\Shop\Application\DTOs;

use App\Modules\Accounting\Domain\ValueObjects\PriceType;

final readonly class ClientContext
{
    public function __construct(
        public ?int $id = null,
        public ?string $uuid = null,
        public string $priceType = PriceType::RETAIL,
        public ?string $ip = null,
        public ?int $region = null,
    ) {}

    public function toArray(): array
    {
        return (array)$this;
    }

    public static function fromArray(array $data): ClientContext
    {
        return new self(
            $data['id'] ?? null,
            $data['uuid'] ?? null,
            $data['priceType'] ?? PriceType::RETAIL,
            $data['ip'] ?? null,
        );
    }
}
