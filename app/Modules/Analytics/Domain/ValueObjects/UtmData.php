<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\ValueObjects;

/**
 * UtmData — UTM-метки из query-параметров запроса.
 */
final class UtmData
{
    public function __construct(
        public readonly ?string $source = null,
        public readonly ?string $medium = null,
        public readonly ?string $campaign = null,
        public readonly ?string $term = null,
        public readonly ?string $content = null,
    ) {
    }

    /**
     * @param array<string, mixed> $query
     */
    public static function fromQuery(array $query): self
    {
        return new self(
            source: self::string($query['utm_source'] ?? null),
            medium: self::string($query['utm_medium'] ?? null),
            campaign: self::string($query['utm_campaign'] ?? null),
            term: self::string($query['utm_term'] ?? null),
            content: self::string($query['utm_content'] ?? null),
        );
    }

    /**
     * Массив в формате, ожидаемом TrafficSource::fromReferrer().
     *
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'utm_source' => $this->source,
            'utm_medium' => $this->medium,
            'utm_campaign' => $this->campaign,
            'utm_term' => $this->term,
            'utm_content' => $this->content,
        ];
    }

    private static function string(mixed $value): ?string
    {
        if (!is_string($value) && !is_numeric($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
