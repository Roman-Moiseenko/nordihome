<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use App\Modules\Analytics\Domain\Entities\SearchEntity;
use DateTimeImmutable;

interface SearchRepositoryInterface
{
    public function findById(int $id): ?SearchEntity;

    /** Записать поисковый запрос. */
    public function create(SearchEntity $search): SearchEntity;

    /** Дополнить клик по результату (clicked_result_*). */
    public function registerClick(int $searchId, int $resultId, string $resultType, int $position): void;

    /**
     * История поиска клиента (для личного кабинета).
     *
     * @return SearchEntity[]
     */
    public function findByVisitor(int $visitorId, int $limit = 50, int $offset = 0): array;

    /**
     * Поиски за период (для агрегаций).
     *
     * @return SearchEntity[]
     */
    public function findInPeriod(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 10000): array;

    /**
     * Поиски по конкретному запросу (для анализа).
     *
     * @return SearchEntity[]
     */
    public function findByNormalizedQuery(string $queryNormalized, DateTimeImmutable $from, DateTimeImmutable $to): array;

    /** Удалить старые записи. */
    public function deleteOlderThan(DateTimeImmutable $before): int;
}
