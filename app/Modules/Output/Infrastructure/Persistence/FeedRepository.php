<?php

declare(strict_types=1);

namespace App\Modules\Output\Infrastructure\Persistence;

use App\Modules\Output\Domain\Entities\FeedEntity;
use App\Modules\Output\Domain\Interfaces\FeedRepositoryInterface;
use App\Modules\Output\Infrastructure\Models\Feed;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FeedRepository implements FeedRepositoryInterface
{
    public function save(FeedEntity $feed): FeedEntity
    {
        $model = $feed->id
            ? Feed::findOrFail($feed->id)
            : new Feed();

        $model->name = $feed->name;
        $model->active = $feed->active ?? false;

        $model->products_in = $feed->productsIn;
        $model->products_out = $feed->productsOut;
        $model->categories_in = $feed->categoriesIn;
        $model->categories_out = $feed->categoriesOut;
        $model->rooms_in = $feed->roomsIn;
        $model->rooms_out = $feed->roomsOut;
        $model->promotions_in = $feed->promotionsIn;
        $model->promotions_out = $feed->promotionsOut;
        $model->groups_in = $feed->groupsIn;
        $model->groups_out = $feed->groupsOut;
        $model->tags_in = $feed->tagsIn;
        $model->tags_out = $feed->tagsOut;

        $model->set_preprice = $feed->setPreprice;
        $model->set_title = $feed->setTitle ?? '';
        $model->set_description = $feed->setDescription ?? '';

        $model->save();

        return $this->hydrate($model->fresh());
    }

    /**
     * @return LengthAwarePaginator<int, FeedEntity>
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Feed::orderByDesc('id')
            ->paginate($perPage)
            ->through(fn(Feed $model) => $this->hydrate($model));
    }

    public function getById(int $id): FeedEntity
    {
        return $this->hydrate(Feed::findOrFail($id));
    }

    public function delete(int $id): void
    {
        Feed::findOrFail($id)->delete();
    }

    private function hydrate(Feed $model): FeedEntity
    {
        $entity = new FeedEntity($model->name);

        $entity->id = $model->id;
        $entity->active = (bool) $model->active;

        $entity->productsIn = $this->toIntArray($model->products_in);
        $entity->productsOut = $this->toIntArray($model->products_out);
        $entity->categoriesIn = $this->toIntArray($model->categories_in);
        $entity->categoriesOut = $this->toIntArray($model->categories_out);
        $entity->roomsIn = $this->toIntArray($model->rooms_in);
        $entity->roomsOut = $this->toIntArray($model->rooms_out);
        $entity->promotionsIn = $this->toIntArray($model->promotions_in);
        $entity->promotionsOut = $this->toIntArray($model->promotions_out);
        $entity->groupsIn = $this->toIntArray($model->groups_in);
        $entity->groupsOut = $this->toIntArray($model->groups_out);
        $entity->tagsIn = $this->toIntArray($model->tags_in);
        $entity->tagsOut = $this->toIntArray($model->tags_out);

        $entity->setPreprice = (bool) $model->set_preprice;
        $entity->setTitle = $model->set_title ?: null;
        $entity->setDescription = $model->set_description ?: null;

        $entity->createdAt = $model->created_at instanceof \DateTimeInterface
            ? \DateTimeImmutable::createFromInterface($model->created_at)
            : null;
        $entity->updatedAt = $model->updated_at instanceof \DateTimeInterface
            ? \DateTimeImmutable::createFromInterface($model->updated_at)
            : null;

        return $entity;
    }

    /**
     * @param array<int, mixed>|null $value
     * @return int[]
     */
    private function toIntArray(?array $value): array
    {
        return array_map('intval', $value ?? []);
    }
}
