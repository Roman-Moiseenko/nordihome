<?php

namespace App\Modules\Lead\Infrastructure\Persistence;

use App\Modules\Lead\Domain\Entities\LeadEntity;
use App\Modules\Lead\Domain\Entities\LeadItemEntity;
use App\Modules\Lead\Domain\Entities\LeadStatusEntity;
use App\Modules\Lead\Domain\Interfaces\LeadRepositoryInterface;
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
        $model->created_at = $lead->createdAt ?? now();

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
                    'created_at' => $itemEntity->createdAt ?? now(),
                    'finished_at' => $itemEntity->finishedAt,
                    'staff_id' => $itemEntity->staffId,
                ]);
                $itemEntity->id = $itemModel->id;
            }
        }

        return $this->hydrate($model->fresh()->load(['items', 'statuses']));
    }

    public function findByOrderId(int $orderId):? LeadEntity
    {
        $model = Lead::where('order_id', $orderId)->first();
        if (is_null($model)) return null;
        return $this->hydrate($model->load(['items', 'statuses']));
    }

    public function findById(int $id): ?LeadEntity
    {
        $model = Lead::where('id', $id)->first();
        if (is_null($model)) return null;
        return $this->hydrate($model->load(['items', 'statuses']));
    }

    public function findByStatus(string $status, ?int $staff_id = null): array
    {
        $query = Lead::query()->orderByDesc('created_at');

        if (!is_null($staff_id)) {
            $query->where('staff_id', $staff_id);
        }

        $query->whereHas('status', function ($query) use ($status) {
            $query->where('value', $status);
        });

        return $query->get()
            ->map(fn(Lead $lead) => $this->hydrate($lead->load(['items', 'statuses'])))
            ->all();
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
        $entity->createdAt = $model->created_at ? \DateTimeImmutable::createFromMutable($model->created_at) : null;
        $entity->updatedAt = $model->updated_at ? \DateTimeImmutable::createFromMutable($model->updated_at) : null;

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
