<?php

namespace App\Modules\Auth\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

final class NewsletterConsent
{
    public const SOURCE_CHECKOUT = 'checkout';
    public const SOURCE_REGISTRATION = 'registration';
    public const SOURCE_MANAGER = 'manager';
    public const SOURCE_POPUP = 'popup';

    private const ALLOWED_SOURCES = [
        self::SOURCE_CHECKOUT,
        self::SOURCE_REGISTRATION,
        self::SOURCE_MANAGER,
        self::SOURCE_POPUP,
    ];

    public bool $consented {
        get => $this->consentedValue;
    }

    public DateTimeImmutable $consentedAt {
        get => $this->consentedAtValue;
        set {
            if (!is_null($value)) $this->consentedAtValue = $value;
        }
    }

    public string $consentTextVersion {
        get => $this->consentTextVersionValue;
    }

    public ?string $actionIdentifier {
        get => $this->actionIdentifierValue;
    }

    public string $source {
        get => $this->sourceValue;
    }

    public bool $active {
        get => $this->activeValue;
    }

    private bool $consentedValue;
    private DateTimeImmutable $consentedAtValue;
    private string $consentTextVersionValue;
    private ?string $actionIdentifierValue;
    private string $sourceValue;
    private bool $activeValue;

    public function __construct(
        string $consentTextVersion,
        string $source,
        ?string $actionIdentifier = null,
        bool $active = true
    ) {
        $this->consentedValue = true;
        $this->consentedAtValue = new DateTimeImmutable(); // всегда текущая дата/время
        $this->consentTextVersionValue = $this->validateConsentTextVersion($consentTextVersion);
        $this->sourceValue = $this->validateSource($source);
        $this->actionIdentifierValue = $actionIdentifier ? trim($actionIdentifier) : null;
        $this->activeValue = $active;
    }

    /**
     * Возвращает новый объект с отозванным согласием.
     * Дата согласия остаётся прежней.
     */
    public function withdraw(): self
    {
        $withdrawn = new self($this->consentTextVersionValue, $this->sourceValue, $this->actionIdentifierValue, false);
        // Перетираем автоматически установленную дату на исходную
        $withdrawn->consentedAtValue = $this->consentedAtValue;
        return $withdrawn;
    }

    public function equals(self $other): bool
    {
        return $this->consentedValue === $other->consentedValue
            && $this->consentedAtValue == $other->consentedAtValue
            && $this->consentTextVersionValue === $other->consentTextVersionValue
            && $this->actionIdentifierValue === $other->actionIdentifierValue
            && $this->sourceValue === $other->sourceValue
            && $this->activeValue === $other->activeValue;
    }

    private function validateConsentTextVersion(string $version): string
    {
        $trimmed = trim($version);
        if (empty($trimmed)) {
            throw new InvalidArgumentException('Версия текста согласия не может быть пустой');
        }
        return $trimmed;
    }

    private function validateSource(string $source): string
    {
        $trimmed = trim($source);
        if (!in_array($trimmed, self::ALLOWED_SOURCES, true)) {
            throw new InvalidArgumentException('Недопустимый источник согласия на рассылку');
        }
        return $trimmed;
    }
}
