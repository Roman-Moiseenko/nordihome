<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Entities\SearchEntity;
use App\Modules\Analytics\Domain\Interfaces\SearchRepositoryInterface;
use App\Modules\Analytics\Infrastructure\Models\Search;
use Carbon\CarbonInterface;
use DateTimeImmutable;

class SearchRepository implements SearchRepositoryInterface
{
    public function findById(int $id): ?SearchEntity
    {
        $model = Search::find($id);

        return $model ? $this->hydrate($model) : null;
    }

    public function create(SearchEntity $search): SearchEntity
    {
        $model = new Search();
        $this->fill($model, $search);
        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function registerClick(int $searchId, int $resultId, string $resultType, int $position): void
    {
        Search::whereKey($searchId)->update([
            'clicked_result_id' => $resultId,
            'clicked_result_type' => $resultType,
            'clicked_position' => $position,
        ]);
    }

    public function findByVisitor(int $visitorId, int $limit = 50, int $offset = 0): array
    {
        return Search::where('visitor_id', $visitorId)
            ->orderByDesc('searched_at')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(fn(Search $model) => $this->hydrate($model))
            ->all();
    }

    public function findInPeriod(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 10000): array
    {
        return Search::whereBetween('searched_at', [$from, $to])
            ->orderBy('searched_at')
            ->limit($limit)
            ->get()
            ->map(fn(Search $model) => $this->hydrate($model))
            ->all();
    }

    public function findByNormalizedQuery(string $queryNormalized, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return Search::where('query_normalized', $queryNormalized)
            ->whereBetween('searched_at', [$from, $to])
            ->orderBy('searched_at')
            ->get()
            ->map(fn(Search $model) => $this->hydrate($model))
            ->all();
    }

    public function deleteOlderThan(DateTimeImmutable $before): int
    {
        return Search::where('searched_at', '<', $before)->delete();
    }

    private function fill(Search $model, SearchEntity $search): void
    {
        $model->visitor_id = $search->visitorId;
        $model->session_id = $search->sessionId;
        $model->page_view_id = $search->pageViewId;
        $model->query = $search->query;
        $model->query_normalized = $search->queryNormalized;
        $model->results_count = $search->resultsCount;
        $model->clicked_result_id = $search->clickedResultId;
        $model->clicked_result_type = $search->clickedResultType;
        $model->clicked_position = $search->clickedPosition;
        $model->searched_at = $search->searchedAt;
        $model->is_bot = $search->isBot;
    }

    private function hydrate(Search $model): SearchEntity
    {
        $search = new SearchEntity(
            visitorId: (int)$model->visitor_id,
            query: $model->query,
            queryNormalized: $model->query_normalized,
            searchedAt: DateTimeImmutable::createFromInterface($model->searched_at),
        );

        $search->id = $model->id;
        $search->sessionId = $model->session_id;
        $search->pageViewId = $model->page_view_id;
        $search->resultsCount = (int)$model->results_count;
        $search->clickedResultId = $model->clicked_result_id;
        $search->clickedResultType = $model->clicked_result_type;
        $search->clickedPosition = $model->clicked_position;
        $search->isBot = (bool)$model->is_bot;
        $search->createdAt = $this->immutable($model->created_at);

        return $search;
    }

    private function immutable(?CarbonInterface $value): ?DateTimeImmutable
    {
        return $value === null ? null : DateTimeImmutable::createFromInterface($value);
    }
}
