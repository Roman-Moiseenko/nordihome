<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\Search;

use App\Modules\Analytics\Domain\Entities\SearchEntity;
use App\Modules\Analytics\Domain\Interfaces\SearchRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\SessionRepositoryInterface;
use DateTimeImmutable;

/**
 * TrackSearch — регистрация поискового запроса.
 *
 * Нормализует строку запроса (нижний регистр + trim) и пишет append-only
 * запись. Обновляет счётчик searches_count сессии.
 */
final readonly class TrackSearchUseCase
{
    public function __construct(
        private SearchRepositoryInterface  $searches,
        private SessionRepositoryInterface $sessions,
    ) {}

    public function execute(
        int $visitorId,
        ?int $sessionId,
        ?int $pageViewId,
        string $query,
        int $resultsCount = 0,
    ): SearchEntity {
        $query = trim($query);
        $normalized = mb_strtolower($query);

        $search = new SearchEntity($visitorId, $query, $normalized, new DateTimeImmutable());
        $search->sessionId = $sessionId;
        $search->pageViewId = $pageViewId;
        $search->resultsCount = max(0, $resultsCount);

        $search = $this->searches->create($search);

        if ($sessionId !== null) {
            $this->sessions->increment($sessionId, 'searches_count');
        }

        return $search;
    }
}
