<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Entities\SourceDailyEntity;
use App\Modules\Analytics\Domain\Interfaces\SourceDailyRepositoryInterface;
use App\Modules\Analytics\Infrastructure\Models\Session;
use App\Modules\Analytics\Infrastructure\Models\SourceDaily;
use DateTimeImmutable;

class SourceDailyRepository implements SourceDailyRepositoryInterface
{
    public function getReport(DateTimeImmutable $from, DateTimeImmutable $to, ?string $source = null): array
    {
        $query = SourceDaily::whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')]);
        if ($source !== null) {
            $query->where('source', $source);
        }

        return $query->orderBy('date')
            ->get()
            ->map(fn(SourceDaily $model) => $this->hydrate($model))
            ->all();
    }

    public function rebuildForDate(DateTimeImmutable $date, DateTimeImmutable $from, DateTimeImmutable $to): void
    {
        SourceDaily::where('date', $date->format('Y-m-d'))->delete();

        $rows = Session::query()
            ->whereBetween('started_at', [$from, $to])
            ->select('source', 'utm_source', 'utm_medium', 'utm_campaign')
            ->selectRaw('COUNT(*) as sessions_count')
            ->selectRaw('COUNT(DISTINCT visitor_id) as unique_visitors_count')
            ->selectRaw('SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as bounce_count')
            ->selectRaw('AVG(duration) as avg_duration')
            ->groupBy('source', 'utm_source', 'utm_medium', 'utm_campaign')
            ->get();

        foreach ($rows as $row) {
            SourceDaily::create([
                'date' => $date->format('Y-m-d'),
                'source' => $row->source ?? 'direct',
                'utm_source' => $row->utm_source,
                'utm_medium' => $row->utm_medium,
                'utm_campaign' => $row->utm_campaign,
                'sessions_count' => (int)$row->sessions_count,
                'unique_visitors_count' => (int)$row->unique_visitors_count,
                'new_visitors_count' => 0,
                'bounce_count' => (int)$row->bounce_count,
                'avg_duration' => $row->avg_duration !== null ? (int)round($row->avg_duration) : null,
            ]);
        }
    }

    public function getTopSources(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 20): array
    {
        return SourceDaily::whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->select('source')
            ->selectRaw('SUM(sessions_count) as sessions_count')
            ->selectRaw('SUM(unique_visitors_count) as unique_visitors_count')
            ->selectRaw('SUM(new_visitors_count) as new_visitors_count')
            ->selectRaw('SUM(bounce_count) as bounce_count')
            ->groupBy('source')
            ->orderByDesc('sessions_count')
            ->limit($limit)
            ->get()
            ->map(fn($row) => $this->hydrateAggregate($row))
            ->all();
    }

    public function getByCampaign(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 20): array
    {
        return SourceDaily::whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->whereNotNull('utm_campaign')
            ->select('utm_campaign', 'utm_source')
            ->selectRaw('SUM(sessions_count) as sessions_count')
            ->selectRaw('SUM(unique_visitors_count) as unique_visitors_count')
            ->groupBy('utm_campaign', 'utm_source')
            ->orderByDesc('sessions_count')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $entity = new SourceDailyEntity((string)$row->utm_campaign, 'utm');
                $entity->utmSource = $row->utm_source;
                $entity->utmCampaign = $row->utm_campaign;
                $entity->sessionsCount = (int)$row->sessions_count;
                $entity->uniqueVisitorsCount = (int)$row->unique_visitors_count;

                return $entity;
            })
            ->all();
    }

    public function deleteOlderThan(DateTimeImmutable $before): int
    {
        return SourceDaily::where('date', '<', $before->format('Y-m-d'))->delete();
    }

    private function hydrate(SourceDaily $model): SourceDailyEntity
    {
        $entity = new SourceDailyEntity($model->date, $model->source);
        $entity->id = $model->id;
        $entity->utmSource = $model->utm_source;
        $entity->utmMedium = $model->utm_medium;
        $entity->utmCampaign = $model->utm_campaign;
        $entity->sessionsCount = (int)$model->sessions_count;
        $entity->uniqueVisitorsCount = (int)$model->unique_visitors_count;
        $entity->newVisitorsCount = (int)$model->new_visitors_count;
        $entity->bounceCount = (int)$model->bounce_count;
        $entity->avgDuration = $model->avg_duration;

        return $entity;
    }

    private function hydrateAggregate($row): SourceDailyEntity
    {
        $entity = new SourceDailyEntity('', (string)$row->source);
        $entity->sessionsCount = (int)$row->sessions_count;
        $entity->uniqueVisitorsCount = (int)$row->unique_visitors_count;
        $entity->newVisitorsCount = (int)$row->new_visitors_count;
        $entity->bounceCount = (int)$row->bounce_count;

        return $entity;
    }
}
