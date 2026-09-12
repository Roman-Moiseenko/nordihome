<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Interfaces\PageDailyRepositoryInterface;
use App\Modules\Analytics\Domain\ReadModels\PageDailyData;
use App\Modules\Analytics\Domain\ReadModels\PageMetricsData;
use App\Modules\Analytics\Infrastructure\Models\PageDaily;
use App\Modules\Analytics\Infrastructure\Models\PageView;
use DateTimeImmutable;

class PageDailyRepository implements PageDailyRepositoryInterface
{
    private const array SORTABLE = ['views_count', 'unique_visitors_count', 'bounce_count'];

    public function getTopByType(DateTimeImmutable $date, string $pageType, int $limit = 20, string $sortBy = 'views_count'): array
    {
        $sort = in_array($sortBy, self::SORTABLE, true) ? $sortBy : 'views_count';

        return PageDaily::where('date', $date->format('Y-m-d'))
            ->where('page_type', $pageType)
            ->orderByDesc($sort)
            ->limit($limit)
            ->get()
            ->map(fn(PageDaily $model) => $this->hydrate($model))
            ->all();
    }

    public function findByEntity(DateTimeImmutable $date, string $pageType, int $entityId): ?PageDailyData
    {
        $model = PageDaily::where('date', $date->format('Y-m-d'))
            ->where('page_type', $pageType)
            ->where('entity_id', $entityId)
            ->first();

        return $model ? $this->hydrate($model) : null;
    }

    public function rebuildForDate(DateTimeImmutable $date, DateTimeImmutable $from, DateTimeImmutable $to): void
    {
        PageDaily::where('date', $date->format('Y-m-d'))->delete();

        $rows = PageView::query()
            ->whereBetween('viewed_at', [$from, $to])
            ->select('page_type', 'entity_id')
            ->selectRaw('COUNT(*) as views_count')
            ->selectRaw('COUNT(DISTINCT visitor_id) as unique_visitors_count')
            ->selectRaw('AVG(duration) as avg_duration')
            ->selectRaw('SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as bounce_count')
            ->groupBy('page_type', 'entity_id')
            ->get();

        foreach ($rows as $row) {
            PageDaily::create([
                'date' => $date->format('Y-m-d'),
                'page_type' => $row->page_type,
                'entity_id' => $row->entity_id,
                'views_count' => (int)$row->views_count,
                'unique_visitors_count' => (int)$row->unique_visitors_count,
                'avg_duration' => $row->avg_duration !== null ? (int)round($row->avg_duration) : null,
                'bounce_count' => (int)$row->bounce_count,
            ]);
        }
    }

    public function getAvgMetrics(string $pageType, DateTimeImmutable $from, DateTimeImmutable $to): ?PageMetricsData
    {
        $row = PageDaily::where('page_type', $pageType)
            ->whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->selectRaw('SUM(views_count) as views_count')
            ->selectRaw('SUM(unique_visitors_count) as unique_visitors_count')
            ->selectRaw('AVG(avg_duration) as avg_duration')
            ->selectRaw('SUM(bounce_count) as bounce_count')
            ->first();

        if ($row === null || (int)$row->views_count === 0) {
            return null;
        }

        return new PageMetricsData(
            pageType: $pageType,
            viewsCount: (int)$row->views_count,
            uniqueVisitorsCount: (int)$row->unique_visitors_count,
            avgDuration: $row->avg_duration !== null ? (int)round($row->avg_duration) : null,
            bounceCount: (int)$row->bounce_count,
        );
    }

    public function deleteOlderThan(DateTimeImmutable $before): int
    {
        return PageDaily::where('date', '<', $before->format('Y-m-d'))->delete();
    }

    private function hydrate(PageDaily $model): PageDailyData
    {
        return new PageDailyData(
            date: $model->date,
            pageType: $model->page_type,
            entityId: $model->entity_id,
            viewsCount: (int)$model->views_count,
            uniqueVisitorsCount: (int)$model->unique_visitors_count,
            avgDuration: $model->avg_duration,
            bounceCount: (int)$model->bounce_count,
        );
    }
}
