<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Entities\ExitEntity;
use App\Modules\Analytics\Domain\Interfaces\ExitRepositoryInterface;
use App\Modules\Analytics\Infrastructure\Models\ExitPoint;
use Carbon\CarbonInterface;
use DateTimeImmutable;

class ExitRepository implements ExitRepositoryInterface
{
    public function create(ExitEntity $exit): void
    {
        $model = new ExitPoint();
        $this->fill($model, $exit);
        $model->save();
    }

    public function findBySession(int $sessionId): array
    {
        return ExitPoint::where('session_id', $sessionId)
            ->orderBy('exit_at')
            ->get()
            ->map(fn(ExitPoint $model) => $this->hydrate($model))
            ->all();
    }

    public function findTopExitPages(DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 20): array
    {
        return ExitPoint::whereBetween('exit_at', [$from, $to])
            ->select('url', 'page_type', 'entity_id')
            ->selectRaw('COUNT(*) as exits')
            ->groupBy('url', 'page_type', 'entity_id')
            ->orderByDesc('exits')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'url' => $row->url,
                'page_type' => $row->page_type,
                'entity_id' => $row->entity_id !== null ? (int)$row->entity_id : null,
                'exits' => (int)$row->exits,
            ])
            ->all();
    }

    public function getAvgDurationByPageType(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return ExitPoint::whereBetween('exit_at', [$from, $to])
            ->select('page_type')
            ->selectRaw('AVG(duration_on_page) as avg_duration')
            ->groupBy('page_type')
            ->pluck('avg_duration', 'page_type')
            ->map(fn($value) => (int)round($value))
            ->all();
    }

    public function deleteOlderThan(DateTimeImmutable $before): int
    {
        return ExitPoint::where('exit_at', '<', $before)->delete();
    }

    private function fill(ExitPoint $model, ExitEntity $exit): void
    {
        $model->visitor_id = $exit->visitorId;
        $model->session_id = $exit->sessionId;
        $model->page_view_id = $exit->pageViewId;
        $model->url = $exit->url;
        $model->page_type = $exit->pageType;
        $model->entity_id = $exit->entityId;
        $model->exit_at = $exit->exitAt;
        $model->duration_on_page = $exit->durationOnPage;
        $model->reason = $exit->reason;
    }

    private function hydrate(ExitPoint $model): ExitEntity
    {
        $exit = new ExitEntity(
            visitorId: (int)$model->visitor_id,
            sessionId: (int)$model->session_id,
            pageViewId: (int)$model->page_view_id,
            url: $model->url,
            pageType: $model->page_type,
            exitAt: DateTimeImmutable::createFromInterface($model->exit_at),
            durationOnPage: (int)$model->duration_on_page,
            reason: $model->reason,
        );

        $exit->id = $model->id;
        $exit->entityId = $model->entity_id;

        return $exit;
    }
}
