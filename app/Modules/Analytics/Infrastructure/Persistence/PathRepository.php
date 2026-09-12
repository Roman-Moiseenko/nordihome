<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Entities\PathStepEntity;
use App\Modules\Analytics\Domain\Interfaces\PathRepositoryInterface;
use App\Modules\Analytics\Infrastructure\Models\Path;
use Carbon\CarbonInterface;
use DateTimeImmutable;

class PathRepository implements PathRepositoryInterface
{
    public function create(PathStepEntity $step): void
    {
        $model = new Path();
        $this->fill($model, $step);
        $model->save();
    }

    public function updateLastDuration(int $sessionId, int $duration): void
    {
        Path::where('session_id', $sessionId)
            ->orderByDesc('step_number')
            ->limit(1)
            ->update(['duration' => $duration]);
    }

    public function findBySession(int $sessionId): array
    {
        return Path::where('session_id', $sessionId)
            ->orderBy('step_number')
            ->get()
            ->map(fn(Path $model) => $this->hydrate($model))
            ->all();
    }

    public function findByVisitor(int $visitorId, int $limit = 200): array
    {
        return Path::where('visitor_id', $visitorId)
            ->orderByDesc('occurred_at')
            ->limit($limit)
            ->get()
            ->map(fn(Path $model) => $this->hydrate($model))
            ->all();
    }

    public function findTopPaths(DateTimeImmutable $from, DateTimeImmutable $to, int $maxSteps = 5, int $limit = 20): array
    {
        $sessions = Path::whereBetween('occurred_at', [$from, $to])
            ->select('session_id', 'page_type')
            ->orderBy('session_id')
            ->orderBy('step_number')
            ->get();

        $paths = [];
        foreach ($sessions as $step) {
            $paths[$step->session_id][] = $step->page_type;
        }

        $counts = [];
        foreach ($paths as $sequence) {
            $sequence = array_slice($sequence, 0, $maxSteps);
            $key = implode(' > ', $sequence);
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        arsort($counts);

        return array_slice(
            array_map(
                static fn(string $key, int $count) => ['path' => explode(' > ', $key), 'count' => $count],
                array_keys($counts),
                $counts
            ),
            0,
            $limit
        );
    }

    public function deleteOlderThan(DateTimeImmutable $before): int
    {
        return Path::where('occurred_at', '<', $before)->delete();
    }

    private function fill(Path $model, PathStepEntity $step): void
    {
        $model->session_id = $step->sessionId;
        $model->visitor_id = $step->visitorId;
        $model->step_number = $step->stepNumber;
        $model->page_type = $step->pageType;
        $model->entity_id = $step->entityId;
        $model->url = $step->url;
        $model->duration = $step->duration;
        $model->action_type = $step->actionType;
        $model->occurred_at = $step->occurredAt;
    }

    private function hydrate(Path $model): PathStepEntity
    {
        $step = new PathStepEntity(
            sessionId: (int)$model->session_id,
            visitorId: (int)$model->visitor_id,
            stepNumber: (int)$model->step_number,
            pageType: $model->page_type,
            url: $model->url,
            occurredAt: DateTimeImmutable::createFromInterface($model->occurred_at),
        );

        $step->id = $model->id;
        $step->entityId = $model->entity_id;
        $step->duration = $model->duration;
        $step->actionType = $model->action_type;

        return $step;
    }
}
