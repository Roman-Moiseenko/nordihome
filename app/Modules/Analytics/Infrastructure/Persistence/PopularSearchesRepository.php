<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Interfaces\PopularSearchesRepositoryInterface;
use App\Modules\Analytics\Domain\ReadModels\PopularSearchData;
use App\Modules\Analytics\Infrastructure\Models\PopularSearch;
use DateTimeImmutable;

class PopularSearchesRepository implements PopularSearchesRepositoryInterface
{
    public function getTop(string $periodType, DateTimeImmutable $periodDate, int $limit = 20): array
    {
        return PopularSearch::where('period_type', $periodType)
            ->where('period_date', $periodDate->format('Y-m-d'))
            ->orderByDesc('searches_count')
            ->limit($limit)
            ->get()
            ->map(fn(PopularSearch $model) => $this->hydrate($model))
            ->all();
    }

    public function rebuildForPeriod(string $periodType, DateTimeImmutable $periodDate, DateTimeImmutable $from, DateTimeImmutable $to): void
    {
        // Удаляем существующий агрегат и пересчитываем из детальной таблицы.
        PopularSearch::where('period_type', $periodType)
            ->where('period_date', $periodDate->format('Y-m-d'))
            ->delete();

        $rows = \App\Modules\Analytics\Infrastructure\Models\Search::query()
            ->whereBetween('searched_at', [$from, $to])
            ->select('query_normalized')
            ->selectRaw('MIN(query) as query_sample')
            ->selectRaw('COUNT(*) as searches_count')
            ->selectRaw('COUNT(DISTINCT visitor_id) as unique_visitors_count')
            ->selectRaw('SUM(CASE WHEN clicked_result_id IS NOT NULL THEN 1 ELSE 0 END) as clicks_count')
            ->groupBy('query_normalized')
            ->get();

        foreach ($rows as $row) {
            PopularSearch::create([
                'query_normalized' => $row->query_normalized,
                'query_sample' => $row->query_sample ?? $row->query_normalized,
                'searches_count' => (int)$row->searches_count,
                'unique_visitors_count' => (int)$row->unique_visitors_count,
                'clicks_count' => (int)$row->clicks_count,
                'period_date' => $periodDate->format('Y-m-d'),
                'period_type' => $periodType,
            ]);
        }
    }

    public function deleteOlderThan(DateTimeImmutable $before, string $periodType): int
    {
        return PopularSearch::where('period_type', $periodType)
            ->where('period_date', '<', $before->format('Y-m-d'))
            ->delete();
    }

    public function findByQuery(string $queryNormalized, string $periodType, DateTimeImmutable $periodDate): ?PopularSearchData
    {
        $model = PopularSearch::where('query_normalized', $queryNormalized)
            ->where('period_type', $periodType)
            ->where('period_date', $periodDate->format('Y-m-d'))
            ->first();

        return $model ? $this->hydrate($model) : null;
    }

    private function hydrate(PopularSearch $model): PopularSearchData
    {
        return new PopularSearchData(
            queryNormalized: $model->query_normalized,
            querySample: $model->query_sample,
            searchesCount: (int)$model->searches_count,
            uniqueVisitorsCount: (int)$model->unique_visitors_count,
            clicksCount: (int)$model->clicks_count,
            periodDate: $model->period_date,
            periodType: $model->period_type,
        );
    }
}
