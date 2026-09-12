<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Entities;

use App\Modules\Analytics\Domain\ValueObjects\TrafficSource;
use DateTimeImmutable;

/**
 * Visitor — верхнеуровневая сущность аналитики: посетитель сайта.
 *
 * Объединяет анонимных гостей (по uuid из cookie) и авторизованных клиентов
 * (по client_id). Хранит снимок первого касания с сайтом и счётчик визитов.
 */
final class VisitorEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $uuid {
        get => $this->uuid;
        set => $this->uuid = $value;
    }

    public ?int $clientId = null {
        get => $this->clientId;
        set => $this->clientId = $value;
    }

    public DateTimeImmutable $firstVisitAt {
        get => $this->firstVisitAt;
        set => $this->firstVisitAt = $value;
    }

    public DateTimeImmutable $lastVisitAt {
        get => $this->lastVisitAt;
        set => $this->lastVisitAt = $value;
    }

    public int $visitsCount = 1 {
        get => $this->visitsCount;
        set => $this->visitsCount = $value;
    }

    public ?string $ip = null {
        get => $this->ip;
        set => $this->ip = $value;
    }

    public ?string $city = null {
        get => $this->city;
        set => $this->city = $value;
    }

    public ?string $region = null {
        get => $this->region;
        set => $this->region = $value;
    }

    public ?string $country = null {
        get => $this->country;
        set => $this->country = $value;
    }

    public ?string $userAgent = null {
        get => $this->userAgent;
        set => $this->userAgent = $value;
    }

    public ?string $deviceType = null {
        get => $this->deviceType;
        set => $this->deviceType = $value;
    }

    public ?string $os = null {
        get => $this->os;
        set => $this->os = $value;
    }

    public ?string $browser = null {
        get => $this->browser;
        set => $this->browser = $value;
    }

    public ?string $referrer = null {
        get => $this->referrer;
        set => $this->referrer = $value;
    }

    public ?TrafficSource $source = null {
        get => $this->source;
        set => $this->source = $value;
    }

    public ?string $utmSource = null {
        get => $this->utmSource;
        set => $this->utmSource = $value;
    }

    public ?string $utmMedium = null {
        get => $this->utmMedium;
        set => $this->utmMedium = $value;
    }

    public ?string $utmCampaign = null {
        get => $this->utmCampaign;
        set => $this->utmCampaign = $value;
    }

    public ?string $utmTerm = null {
        get => $this->utmTerm;
        set => $this->utmTerm = $value;
    }

    public ?string $utmContent = null {
        get => $this->utmContent;
        set => $this->utmContent = $value;
    }

    public ?string $landingUrl = null {
        get => $this->landingUrl;
        set => $this->landingUrl = $value;
    }

    public ?DateTimeImmutable $clientLinkedAt = null {
        get => $this->clientLinkedAt;
        set => $this->clientLinkedAt = $value;
    }

    public bool $isBot = false {
        get => $this->isBot;
        set => $this->isBot = $value;
    }

    public ?DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public ?DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    public function __construct(
        string $uuid,
        DateTimeImmutable $firstVisitAt,
        DateTimeImmutable $lastVisitAt,
        ?TrafficSource $source = null,
    ) {
        $this->uuid = $uuid;
        $this->firstVisitAt = $firstVisitAt;
        $this->lastVisitAt = $lastVisitAt;
        $this->source = $source;
    }

    /**
     * Фиксирует новый визит: увеличивает счётчик и обновляет lastVisitAt.
     */
    public function recordVisit(DateTimeImmutable $visitedAt): void
    {
        $this->visitsCount++;
        $this->lastVisitAt = $visitedAt;
    }

    /**
     * Привязывает анонимного посетителя к клиенту после логина.
     */
    public function linkClient(int $clientId, ?DateTimeImmutable $linkedAt = null): void
    {
        $this->clientId = $clientId;
        $this->clientLinkedAt = $linkedAt ?? new DateTimeImmutable();
    }

    public function markAsBot(): void
    {
        $this->isBot = true;
    }

    public function isAnonymous(): bool
    {
        return $this->clientId === null;
    }
}
