<?php

namespace App\Modules\Lead\Infrastructure\Persistence;

use App\Modules\Lead\Application\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Entities\LeadItemEntity;
use App\Modules\Lead\Domain\Entities\LeadStatusEntity;
use App\Modules\Lead\Domain\ValueObjects\LeadDataField;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Lead\Infrastructure\Models\Lead;

class LeadRepository implements LeadRepositoryInterface
{
    public function save(LeadEntity $lead): LeadEntity
    {
        $model = $lead->id
            ? Lead::with(['items', 'statuses'])->findOrFail($lead->id)
            : new Lead();

        $model->staff_id = $lead->staffId;
        $model->client_id = $lead->clientId;
        $model->order_id = $lead->orderId;
        $model->leadable_id = $lead->leadableId;
        $model->leadable_type = $lead->leadableType;
        $model->name = $lead->name;
        $model->comment = $lead->comment ?? '';
        $model->canceled = $lead->canceled;
        $model->completed = $lead->completed;
        $model->assembly = $lead->assembly;
        $model->delivery = $lead->delivery;
        $model->finished_at = $lead->finishedAt;

        $model->data = array_map(
            fn(LeadDataField $field) => ['name' => $field->getName(), 'value' => $field->getValue()],
            $lead->data
        );

        $model->save();

        // Сохраняем статусы
        foreach ($lead->statuses as $statusEntity) {
            if (!$statusEntity->id) {
                $statusModel = $model->statuses()->create([
                    'value' => (string) $statusEntity->value,
                    'created_at' => $statusEntity->createdAt,
                ]);
                $statusEntity->id = $statusModel->id;
            }
        }

        // Сохраняем элементы
        foreach ($lead->items as $itemEntity) {
            if (!$itemEntity->id) {
                $itemModel = $model->items()->create([
                    'comment' => $itemEntity->comment,
                    'created_at' => $itemEntity->createdAt,
                    'finished_at' => $itemEntity->finishedAt,
                ]);
                $itemEntity->id = $itemModel->id;
            }
        }

        return $this->hydrate($model->fresh()->load(['items', 'statuses']));
    }

    private function hydrate(Lead $model): LeadEntity
    {
        $data = array_map(function ($item) {
            return LeadDataField::fromArray($item);
        }, $model->data);

        $entity = new LeadEntity(
            leadableId: $model->leadable_id,
            leadableType: $model->leadable_type,
            data: $data,
        );

        $entity->name = $model->name;
        $entity->id = $model->id;
        $entity->staffId = $model->staff_id;
        $entity->clientId = $model->client_id;
        $entity->orderId = $model->order_id;
        $entity->comment = $model->comment;
        $entity->canceled = $model->canceled;
        $entity->completed = $model->completed;
        $entity->assembly = $model->assembly;
        $entity->delivery = $model->delivery;
        $entity->finishedAt = $model->finished_at ? \DateTimeImmutable::createFromMutable($model->finished_at) : null;

        // Гидрация items
        if ($model->relationLoaded('items')) {
            foreach ($model->items as $itemModel) {
                $item = new LeadItemEntity(
                    comment: $itemModel->comment,
                    staffId: $itemModel->staff_id,
                );
                $item->id = $itemModel->id;
                $item->type = $itemModel->type;
                $item->createdAt = $itemModel->created_at ? \DateTimeImmutable::createFromMutable($itemModel->created_at) : new \DateTimeImmutable();
                $item->finishedAt = $itemModel->finished_at ? \DateTimeImmutable::createFromMutable($itemModel->finished_at) : null;
                $entity->addItem($item);
            }
        }

        // Гидрация статусов
        if ($model->relationLoaded('statuses')) {
            foreach ($model->statuses as $statusModel) {
                $status = new LeadStatusEntity(
                    value: new LeadStatusValue($statusModel->value),
                );
                $status->id = $statusModel->id;
                $status->createdAt = $statusModel->created_at ? \DateTimeImmutable::createFromMutable($statusModel->created_at) : new \DateTimeImmutable();
                $entity->addStatusEntity($status);
            }

            // Текущий статус — последний
            if (!empty($entity->statuses)) {
                $entity->status = $entity->statuses[array_key_last($entity->statuses)];
            }
        }

        return $entity;
    }
}
