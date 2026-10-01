<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence;

use App\Modules\Accounting\Domain\Entities\PriceOutboxEntity;
use App\Modules\Accounting\Domain\Interfaces\PriceOutboxRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Models\PriceOutbox;

class PriceOutboxRepository implements PriceOutboxRepositoryInterface
{
    public function getAll(): array
    {
        return PriceOutbox::query()
            ->get()
            ->map(fn(PriceOutbox $model) => $this->hydrate($model))
            ->all();
    }

    public function getById(int $id): PriceOutboxEntity
    {
        return $this->hydrate(PriceOutbox::query()->findOrFail($id));
    }

    public function save(PriceOutboxEntity $outbox): PriceOutboxEntity
    {
        $model = $outbox->id
            ? PriceOutbox::query()->findOrFail($outbox->id)
            : new PriceOutbox();

        $model->code = $outbox->code;
        $model->price = $outbox->price;
        $model->price_ikea = $outbox->priceIkea;
        $model->progress = $outbox->isProgress();

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        PriceOutbox::query()->findOrFail($id)->delete();
    }

    public function markAllAsProgress(): void
    {
        PriceOutbox::query()->update(['progress' => true]);
    }

    public function deleteAllWithProgress(): void
    {
        PriceOutbox::query()->where('progress', true)->delete();
    }

    public function resetProgress(): void
    {
        PriceOutbox::query()
            ->where('progress', true)
            ->update(['progress' => false]);
    }

    private function hydrate(PriceOutbox $model): PriceOutboxEntity
    {
        $entity = new PriceOutboxEntity(
            code: $model->code,
            price: $model->price,
            priceIkea: $model->price_ikea,
        );

        $entity->id = $model->id;
        $entity->progress = (bool) $model->progress;

        return $entity;
    }
}
