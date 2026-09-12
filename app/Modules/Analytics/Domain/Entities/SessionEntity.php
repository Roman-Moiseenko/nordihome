<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Entities;

use App\Modules\Analytics\Domain\ValueObjects\TrafficSource;
use DateTimeImmutable;

/**
 * Session — визит (сессия) посетителя на сайте.
 *
 * Хранит временные рамки визита, счётчики активности, страницы входа/выхода,
 * источник входа и параметры окружения. Связана с VisitorEntity через visitorId.
 */
final class SessionEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $visitorId {
        get => $this->visitorId;
        set => $this->visitorId = $value;
    }

    public DateTimeImmutable $startedAt {
        get => $this->startedAt;
        set => $this->startedAt = $value;
    }

    public DateTimeImmutable $lastActivityAt {
        get => $this->lastActivityAt;
        set => $this->lastActivityAt = $value;
    }

    public ?DateTimeImmutable $endedAt = null {
        get => $this->endedAt;
        set => $this->endedAt = $value;
    }

    public ?int $duration = null {
        get => $this->duration;
        set => $this->duration = $value;
    }

    public int $pageViewsCount = 0 {
        get => $this->pageViewsCount;
        set => $this->pageViewsCount = $value;
    }

    public int $actionsCount = 0 {
        get => $this->actionsCount;
        set => $this->actionsCount = $value;
    }

    public int $searchesCount = 0 {
        get => $this->searchesCount;
        set => $this->searchesCount = $value;
    }

    public string $entryUrl {
        get => $this->entryUrl;
        set => $this->entryUrl = $value;
    }

    public string $entryPageType {
        get => $this->entryPageType;
        set => $this->entryPageType = $value;
    }

    public ?int $entryEntityId = null {
        get => $this->entryEntityId;
        set => $this->entryEntityId = $value;
    }

    public ?string $exitUrl = null {
        get => $this->exitUrl;
        set => $this->exitUrl = $value;
    }

    public ?string $exitPageType = null {
        get => $this->exitPageType;
        set => $this->exitPageType = $value;
    }

    public ?int $exitEntityId = null {
        get => $this->exitEntityId;
        set => $this->exitEntityId = $value;
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

    public bool $isBounce = false {
        get => $this->isBounce;
        set => $this->isBounce = $value;
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
        int $visitorId,
        DateTimeImmutable $startedAt,
        DateTimeImmutable $lastActivityAt,
        string $entryUrl,
        string $entryPageType,
        ?TrafficSource $source = null,
    ) {
        $this->visitorId = $visitorId;
        $this->startedAt = $startedAt;
        $this->lastActivityAt = $lastActivityAt;
        $this->entryUrl = $entryUrl;
        $this->entryPageType = $entryPageType;
        $this->source = $source;
    }

    /**
     * Обновляет последнюю активность в сессии.
     */
    public function touch(DateTimeImmutable $activityAt): void
    {
        $this->lastActivityAt = $activityAt;
    }

    /**
     * Регистрирует просмотр страницы.
     */
    public function registerPageView(): void
    {
        $this->pageViewsCount++;
    }

    /**
     * Регистрирует действие клиента.
     */
    public function registerAction(): void
    {
        $this->actionsCount++;
    }

    /**
     * Регистрирует поисковый запрос.
     */
    public function registerSearch(): void
    {
        $this->searchesCount++;
    }

    /**
     * Завершает сессию и вычисляет длительность в секундах.
     */
    public function finish(DateTimeImmutable $endedAt): void
    {
        $this->endedAt = $endedAt;
        $this->duration = max(0, $endedAt->getTimestamp() - $this->startedAt->getTimestamp());
    }

    public function markAsBot(): void
    {
        $this->isBot = true;
    }

    public function isFinished(): bool
    {
        return $this->endedAt !== null;
    }
}
