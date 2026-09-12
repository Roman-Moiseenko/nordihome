<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use App\Modules\Analytics\Domain\Entities\PathStepEntity;
use DateTimeImmutable;

interface PathRepositoryInterface
{
    /** Записать шаг пути. */
    public function create(PathStepEntity $step): void;

    /** Проставить duration последнему шагу сессии (при выходе со страницы). */
    public function updateLastDuration(int $sessionId, int $duration): void;

    /**
     * Все шаги сессии по порядку.
     *
     * @return PathStepEntity[]
     */
    public function findBySession(int $sessionId): array;

    /**
     * Все шаги визита конкретного посетителя.
     *
     * @return PathStepEntity[]
     */
    public function findByVisitor(int $visitorId, int $limit = 200): array;

    /**
     * Топ путей (последовательность page_type) за период.
     *
     * @return array<int, array{path: string[], count: int}>
     */
    public function findTopPaths(DateTimeImmutable $from, DateTimeImmutable $to, int $maxSteps = 5, int $limit = 20): array;

    /** Удалить старые шаги. */
    public function deleteOlderThan(DateTimeImmutable $before): int;
}
