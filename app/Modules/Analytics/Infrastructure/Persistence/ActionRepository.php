<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Persistence;

use App\Modules\Analytics\Domain\Entities\ActionEntity;
use App\Modules\Analytics\Domain\Interfaces\ActionRepositoryInterface;
use App\Modules\Analytics\Infrastructure\Models\Action;
use Carbon\CarbonInterface;
use DateTimeImmutable;

class ActionRepository implements ActionRepositoryInterface
{
    public function findById(int $id): ?ActionEntity
    {
        $model = Action::find($id);

        return $model ? $this->hydrate($model) : null;
    }

    public function create(ActionEntity $action): ActionEntity
    {
        $model = new Action();
        $this->fill($model, $action);
        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function findBySession(int $sessionId): array
    {
        return Action::where('session_id', $sessionId)
            ->orderBy('occurred_at')
            ->get()
            ->map(fn(Action $model) => $this->hydrate($model))
            ->all();
    }

    public function findByVisitor(int $visitorId, int $limit = 100, int $offset = 0): array
    {
        return Action::where('visitor_id', $visitorId)
            ->orderByDesc('occurred_at')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(fn(Action $model) => $this->hydrate($model))
            ->all();
    }

    public function findByTypeInPeriod(string $actionType, DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 5000): array
    {
        return Action::where('action_type', $actionType)
            ->whereBetween('occurred_at', [$from, $to])
            ->orderBy('occurred_at')
            ->limit($limit)
            ->get()
            ->map(fn(Action $model) => $this->hydrate($model))
            ->all();
    }

    public function countByEntity(string $actionType, string $entityType, int $entityId, DateTimeImmutable $from, DateTimeImmutable $to): int
    {
        return Action::where('action_type', $actionType)
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->whereBetween('occurred_at', [$from, $to])
            ->count();
    }

    public function deleteOlderThan(DateTimeImmutable $before): int
    {
        return Action::where('occurred_at', '<', $before)->delete();
    }

    private function fill(Action $model, ActionEntity $action): void
    {
        $model->visitor_id = $action->visitorId;
        $model->session_id = $action->sessionId;
        $model->page_view_id = $action->pageViewId;
        $model->action_type = $action->actionType;
        $model->entity_type = $action->entityType;
        $model->entity_id = $action->entityId;
        $model->payload = $action->payload;
        $model->occurred_at = $action->occurredAt;
        $model->is_bot = $action->isBot;
    }

    private function hydrate(Action $model): ActionEntity
    {
        $action = new ActionEntity(
            visitorId: (int)$model->visitor_id,
            sessionId: (int)$model->session_id,
            actionType: $model->action_type,
            occurredAt: DateTimeImmutable::createFromInterface($model->occurred_at),
            payload: $model->payload,
        );

        $action->id = $model->id;
        $action->pageViewId = $model->page_view_id;
        $action->entityType = $model->entity_type;
        $action->entityId = $model->entity_id;
        $action->isBot = (bool)$model->is_bot;
        $action->createdAt = $this->immutable($model->created_at);

        return $action;
    }

    private function immutable(?CarbonInterface $value): ?DateTimeImmutable
    {
        return $value === null ? null : DateTimeImmutable::createFromInterface($value);
    }
}
