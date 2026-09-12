<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use App\Modules\Analytics\Domain\Entities\SessionEntity;
use DateTimeImmutable;

interface SessionRepositoryInterface
{
    public function findById(int $id): ?SessionEntity;

    /** Найти активную сессию посетителя (последняя без ended_at, не старше $window). */
    public function findActiveByVisitor(int $visitorId, DateTimeImmutable $window): ?SessionEntity;

    /** Создать новую сессию. */
    public function create(SessionEntity $session): SessionEntity;

    /** Обновить (last_activity_at, счётчики, exit-данные). */
    public function update(SessionEntity $session): void;

    /** Закрыть сессию: ended_at, duration, exit_*. */
    public function close(int $sessionId, DateTimeImmutable $endedAt, int $duration): void;

    /**
     * Найти сессии, которые надо закрыть по таймауту.
     *
     * @return SessionEntity[]
     */
    public function findExpired(DateTimeImmutable $threshold, int $limit = 500): array;

    /** Инкрементировать счётчик (page_views_count / actions_count / searches_count). */
    public function increment(int $sessionId, string $field, int $by = 1): void;

    /** Обновить last_activity_at. */
    public function touch(int $sessionId, DateTimeImmutable $at): void;

    /**
     * Все сессии посетителя (для отчёта «путь клиента»).
     *
     * @return SessionEntity[]
     */
    public function findByVisitor(int $visitorId, int $limit = 50, int $offset = 0): array;

    /**
     * Сессии за период для агрегаций.
     *
     * @return SessionEntity[]
     */
    public function findInPeriod(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 1000): array;
}
