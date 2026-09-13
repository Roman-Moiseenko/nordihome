<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use App\Modules\Analytics\Domain\Entities\PageViewEntity;
use DateTimeImmutable;

interface PageViewRepositoryInterface
{
    public function findById(int $id): ?PageViewEntity;

    /** Создать просмотр. */
    public function create(PageViewEntity $view): PageViewEntity;

    /** Найти последний просмотр посетителя в сессии (для расчёта duration). */
    public function findLastByVisitorInSession(int $visitorId, int $sessionId): ?PageViewEntity;

    /** Найти последний просмотр посетителя в любых сессиях (для привязки action). */
    public function findLastByVisitor(int $visitorId): ?PageViewEntity;

    /** Проставить duration и scroll_depth для конкретного просмотра. */
    public function finalizeView(int $pageViewId, int $duration, ?int $scrollDepth, bool $isExit): void;

    /** Пометить просмотр как exit. */
    public function markAsExit(int $pageViewId, DateTimeImmutable $at): void;

    /** Пометить просмотр как bounce (единственный в сессии). */
    public function markAsBounce(int $pageViewId): void;

    /**
     * Все просмотры сессии (для отчёта «путь клиента»).
     *
     * @return PageViewEntity[]
     */
    public function findBySession(int $sessionId): array;

    /** Количество просмотров в сессии (для определения bounce). */
    public function countBySession(int $sessionId): int;

    /**
     * Все просмотры посетителя (для истории).
     *
     * @return PageViewEntity[]
     */
    public function findByVisitor(int $visitorId, int $limit = 100, int $offset = 0): array;

    /**
     * Просмотры по типу страницы за период (для агрегатов).
     *
     * @return PageViewEntity[]
     */
    public function findInPeriod(DateTimeImmutable $from, DateTimeImmutable $to, ?string $pageType = null, int $limit = 5000): array;

    /**
     * Топ просматриваемых сущностей за период.
     *
     * @return array<int, array{entity_id: int, views: int}>
     */
    public function findTopEntities(DateTimeImmutable $from, DateTimeImmutable $to, string $pageType, int $limit = 20): array;

    /** Удалить детальные данные старше N дней (retention). */
    public function deleteOlderThan(DateTimeImmutable $before): int;
}
