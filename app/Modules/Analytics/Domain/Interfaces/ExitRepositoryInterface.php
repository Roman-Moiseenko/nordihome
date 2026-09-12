<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use App\Modules\Analytics\Domain\Entities\ExitEntity;
use DateTimeImmutable;

interface ExitRepositoryInterface
{
    /** Записать точку выхода. */
    public function create(ExitEntity $exit): void;

    /**
     * Точки выхода сессии.
     *
     * @return ExitEntity[]
     */
    public function findBySession(int $sessionId): array;

    /**
     * Топ страниц выхода за период.
     *
     * @return array<int, array{url: string, page_type: string, entity_id: int|null, exits: int}>
     */
    public function findTopExitPages(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 20): array;

    /**
     * Среднее время на последней странице по типу.
     *
     * @return array<string, int>
     */
    public function getAvgDurationByPageType(DateTimeImmutable $from, DateTimeImmutable $to): array;

    /** Удалить старые записи. */
    public function deleteOlderThan(DateTimeImmutable $before): int;
}
