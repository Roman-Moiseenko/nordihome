<?php

namespace App\Modules\Order\Infrastructure\Persistence;

use App\Modules\Order\Domain\Entities\OrderLoggerEntity;
use App\Modules\Order\Domain\Interfaces\OrderLoggerRepositoryInterface;
use App\Modules\Order\Infrastructure\Models\OrderLogger;

class OrderLoggerRepository implements OrderLoggerRepositoryInterface
{
    public function save(OrderLoggerEntity $entity): OrderLoggerEntity
    {
        $model = $entity->id
            ? OrderLogger::findOrFail($entity->id)
            : new OrderLogger();

        $model->order_id = $entity->orderId;
        $model->staff_id = $entity->staffId;
        $model->action = $entity->action;
        $model->object = $entity->object;
        $model->old = $entity->old;
        $model->value = $entity->value;
        $model->link = $entity->link;

        if ($entity->createdAt !== null) {
            $model->created_at = $entity->createdAt;
        } elseif (!$model->exists) {
            $model->created_at = now();
        }

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function getByOrderId(int $orderId): array
    {
        return OrderLogger::query()
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn(OrderLogger $model) => $this->hydrate($model))
            ->all();
    }

    private function hydrate(OrderLogger $model): OrderLoggerEntity
    {
        $entity = new OrderLoggerEntity(
            orderId: $model->order_id,
            staffId: $model->staff_id,
            action: $model->action,
        );

        $entity->id = $model->id;
        $entity->object = $model->object;
        $entity->old = $model->old;
        $entity->value = $model->value;
        $entity->link = $model->link;
        $entity->createdAt = $model->created_at
            ? \DateTimeImmutable::createFromInterface($model->created_at)
            : null;

        return $entity;
    }
}
