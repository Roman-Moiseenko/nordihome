<?php

declare(strict_types=1);

namespace App\Modules\Discount\Infrastructure\Persistence;

use App\Modules\Discount\Application\Interfaces\PromotionRepositoryInterface;
use App\Modules\Discount\Domain\Entities\PromotionEntity;
use App\Modules\Discount\Domain\ValueObjects\PromotionStatus;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Pagination\LengthAwarePaginator;

class PromotionRepository implements PromotionRepositoryInterface
{
    public function getById(int $id): PromotionEntity
    {
        $model = Promotion::findOrFail($id);

        return $this->hydrate($model);
    }

    public function save(PromotionEntity $promotion): PromotionEntity
    {
        $model = $promotion->id
            ? Promotion::findOrFail($promotion->id)
            : new Promotion();

        $model->name = $promotion->name;
        $model->title = $promotion->title;
        $model->slug = (string) $promotion->slug;
        $model->description = $promotion->description;
        $model->condition_url = $promotion->conditionUrl;
        $model->menu = $promotion->menu;
        $model->show_title = $promotion->showTitle;
        $model->discount = $promotion->discount;
        $model->published = $promotion->published;
        $model->active = $promotion->active;
        $model->start_at = $promotion->startAt?->format('Y-m-d');
        $model->finish_at = $promotion->finishAt?->format('Y-m-d');
        $model->color_class = $promotion->colorClass;
        $model->position_class = $promotion->positionClass;
        $model->text_tag = $promotion->textTag;
        $model->show_tag = $promotion->showTag;
        $model->show_discount = $promotion->showDiscount;
        $model->svg = $promotion->svg;
        $model->status = $promotion->status?->value();

        $model->save();

        return $this->hydrate($model);
    }

    public function getAllStarted(): array
    {
        return Promotion::where('status', PromotionStatus::STARTED)
            ->orderByDesc('finish_at')
            ->get()
            ->map(fn(Promotion $model) => $this->hydrate($model))
            ->all();
    }

    public function getAll(): array
    {
        return Promotion::orderByDesc('finish_at')
            ->orderBy('start_at')
            ->get()
            ->map(fn(Promotion $model) => $this->hydrate($model))
            ->all();
    }

    public function getAllPaginated(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return Promotion::orderByDesc('finish_at')
            ->orderBy('start_at')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Promotion $model) => $this->hydrate($model));
    }

    public function delete(int $id): void
    {
        Promotion::where('id', $id)->delete();
    }

    private function hydrate(Promotion $model): PromotionEntity
    {
        $entity = new PromotionEntity(
            name: $model->name,
            slug: new Slug($model->slug),
        );

        $entity->id = $model->id;
        $entity->title = $model->title ?? '';
        $entity->description = $model->description ?? '';
        $entity->conditionUrl = $model->condition_url ?? '';
        $entity->menu = (bool) $model->menu;
        $entity->showTitle = (bool) $model->show_title;
        $entity->discount = (int) $model->discount;
        $entity->published = (bool) $model->published;
        $entity->active = (bool) $model->active;
        $entity->colorClass = $model->color_class ?? 'red';
        $entity->positionClass = $model->position_class ?? 'top-right';
        $entity->textTag = $model->text_tag ?? 'Акция';
        $entity->showTag = (bool) $model->show_tag;
        $entity->showDiscount = (bool) $model->show_discount;
        $entity->svg = $model->svg;
        $entity->status = $model->status !== null
            ? new PromotionStatus($model->status)
            : PromotionStatus::default();

        if ($model->start_at !== null) {
            $entity->startAt = \DateTimeImmutable::createFromInterface($model->start_at);
        }

        if ($model->finish_at !== null) {
            $entity->finishAt = \DateTimeImmutable::createFromInterface($model->finish_at);
        }

        return $entity;
    }
}
