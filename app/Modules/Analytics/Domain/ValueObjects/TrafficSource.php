<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * TrafficSource — источник трафика первого визита.
 *
 * Классификация производится по referrer и utm-параметрам:
 * - direct     — referrer пустой;
 * - google, yandex, bing, mail.ru, duckduckgo — поисковики/порталы по домену;
 * - vk, telegram, facebook, instagram — соцсети по домену;
 * - utm        — задан utm_source, но referrer не распознан;
 * - internal   — referrer с того же домена;
 * - other      — всё остальное.
 */
final class TrafficSource
{
    public const string DIRECT = 'direct';
    public const string GOOGLE = 'google';
    public const string YANDEX = 'yandex';
    public const string BING = 'bing';
    public const string MAIL_RU = 'mail.ru';
    public const string DUCKDUCKGO = 'duckduckgo';
    public const string VK = 'vk';
    public const string TELEGRAM = 'telegram';
    public const string FACEBOOK = 'facebook';
    public const string INSTAGRAM = 'instagram';
    public const string UTM = 'utm';
    public const string INTERNAL = 'internal';
    public const string OTHER = 'other';

    /**
     * Карта «источник => маркеры в referrer».
     *
     * @var array<string, string[]>
     */
    private const array DOMAIN_MARKERS = [
        self::GOOGLE => ['google.'],
        self::YANDEX => ['yandex.'],
        self::BING => ['bing.'],
        self::MAIL_RU => ['mail.ru'],
        self::DUCKDUCKGO => ['duckduckgo.'],
        self::VK => ['vk.com'],
        self::TELEGRAM => ['t.me', 'telegram.'],
        self::FACEBOOK => ['facebook.'],
        self::INSTAGRAM => ['instagram.'],
    ];

    private string $value;

    public function __construct(string $value)
    {
        $normalized = strtolower(trim($value));

        if (!self::supports($normalized)) {
            throw new InvalidArgumentException("Недопустимый источник трафика: {$value}");
        }

        $this->value = $normalized;
    }

    /**
     * Классифицирует источник трафика по referrer и utm-параметрам.
     *
     * @param array<string, string|null> $utm
     */
    public static function fromReferrer(string $referrer, array $utm = []): self
    {
        $referrer = trim($referrer);

        if ($referrer === '') {
            return new self(self::DIRECT);
        }

        $known = self::matchKnownSource($referrer);
        if ($known !== null) {
            return new self($known);
        }

        if (self::isInternal($referrer)) {
            return new self(self::INTERNAL);
        }

        $utmSource = trim((string)($utm['utm_source'] ?? ''));
        if ($utmSource !== '') {
            return new self(self::UTM);
        }

        return new self(self::OTHER);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function isDirect(): bool
    {
        return $this->value === self::DIRECT;
    }

    public static function supports(string $value): bool
    {
        return in_array(strtolower(trim($value)), self::values(), true);
    }

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::DIRECT,
            self::GOOGLE,
            self::YANDEX,
            self::BING,
            self::MAIL_RU,
            self::DUCKDUCKGO,
            self::VK,
            self::TELEGRAM,
            self::FACEBOOK,
            self::INSTAGRAM,
            self::UTM,
            self::INTERNAL,
            self::OTHER,
        ];
    }

    private static function matchKnownSource(string $referrer): ?string
    {
        $haystack = strtolower($referrer);

        foreach (self::DOMAIN_MARKERS as $source => $markers) {
            foreach ($markers as $marker) {
                if (str_contains($haystack, $marker)) {
                    return $source;
                }
            }
        }

        return null;
    }

    private static function isInternal(string $referrer): bool
    {
        $host = parse_url($referrer, PHP_URL_HOST);
        if (!is_string($host) || $host === '') {
            return false;
        }

        $appHost = parse_url((string)config('app.url'), PHP_URL_HOST);

        return is_string($appHost)
            && $appHost !== ''
            && self::normalizeHost($host) === self::normalizeHost($appHost);
    }

    private static function normalizeHost(string $host): string
    {
        $normalized = strtolower($host);

        return preg_replace('/^www\./', '', $normalized) ?? $normalized;
    }
}
