<?php

declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Persistence;

use App\Modules\Base\Entity\Meta as BaseMeta;
use App\Modules\Content\Domain\Entities\PageEntity;
use App\Modules\Content\Domain\Interfaces\PageRepositoryInterface;
use App\Modules\Content\Infrastructure\Models\Page;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use DateTimeImmutable;

class PageRepository implements PageRepositoryInterface
{
    /** @return PageEntity[] */
    public function getAll(): array
    {
        return Page::orderBy('sort')
            ->get()
            ->map(fn(Page $model) => $this->hydrate($model))
            ->all();
    }
    /** @return PageEntity[] */
    public function getRootAll(): array
    {
        return Page::orderBy('sort')
            ->where('parent_id', null)
            ->get()
            ->map(fn(Page $model) => $this->hydrate($model))
            ->all();
    }
    public function getById(int $id): PageEntity
    {
        $model = Page::findOrFail($id);
        return $this->hydrate($model);
    }

    public function save(PageEntity $page): PageEntity
    {
        $model = $page->id
            ? Page::findOrFail($page->id)
            : new Page();

        $model->name = $page->name;
        $model->slug = (string) $page->slug;
        $model->svg = $page->svg;
        $model->text = $page->text ?? '';
        $model->template = $page->template;
        $model->sort = $page->id === null
            ? Page::where('parent_id', $page->parentId)->max('sort') + 1
            : ($page->sort ?? 0);
        $model->published = $page->isPublished();
        $model->parent_id = $page->parentId;
        $model->old_render = $page->oldRender;
        $model->meta = new BaseMeta(
            title: $page->meta?->getTitle() ?? '',
            description: $page->meta?->getDescription() ?? '',
        );

        if ($page->publishedAt !== null) {
            $model->published_at = $page->publishedAt->format('Y-m-d H:i:s');
        }

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        $model = Page::findOrFail($id);
        $model->delete();
    }

    public function existsSlug(string $slug, ?int $excludeId = null): bool
    {
        $query = Page::where('slug', $slug);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    public function move(int $id, int $position): void
    {
        /** @var Page $model */
        $model = Page::findOrFail($id);

        $siblings = Page::where('parent_id', $model->parent_id)
            ->where('id', '!=', $model->id)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        $position = max(0, min($position, $siblings->count()));

        $orderedIds = $siblings->pluck('id')->all();
        array_splice($orderedIds, $position, 0, [$model->id]);

        foreach ($orderedIds as $index => $orderedId) {
            Page::where('id', $orderedId)->update(['sort' => $index]);
        }
    }

    private function hydrate(Page $model): PageEntity
    {
        $entity = new PageEntity(
            name: $model->name,
            slug: new Slug($model->slug),
            template: $model->template,
            parentId: $model->parent_id,
        );

        $entity->id = $model->id;
        $entity->svg = $model->svg;
        $entity->text = $model->text;
        $entity->sort = $model->sort ?? 0;
        $entity->published = (bool) $model->published;
        $entity->oldRender = (bool) $model->old_render;

        if ($model->published_at !== null) {
            $entity->publishedAt = new DateTimeImmutable($model->published_at->toDateTimeString());
        }
        if ($model->created_at !== null) {
            $entity->createdAt = new DateTimeImmutable($model->created_at->toDateTimeString());
        }
        if ($model->updated_at !== null) {
            $entity->updatedAt = new DateTimeImmutable($model->updated_at->toDateTimeString());
        }

        // Meta
        $baseMeta = $model->meta;
        $entity->meta = $baseMeta instanceof BaseMeta
            ? new Meta(
                title: $baseMeta->title ?? '',
                description: $baseMeta->description ?? '',
            )
            : new Meta();

        return $entity;
    }
}
