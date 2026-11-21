<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\ValueObjects;

/**
 * Географические координаты (широта/долгота) фактического адреса организации.
 */
final class Geo
{
    public function __construct(
        public readonly string $latitude = '',
        public readonly string $longitude = '',
    ) {
    }

    /**
     * @param array<string, mixed>|null $data
     */
    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self();
        }

        return new self(
            latitude: (string) ($data['latitude'] ?? ''),
            longitude: (string) ($data['longitude'] ?? ''),
        );
    }

    /**
     * @return array{latitude: string, longitude: string}
     */
    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
