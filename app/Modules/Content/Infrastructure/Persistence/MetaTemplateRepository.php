<?php

namespace App\Modules\Content\Infrastructure\Persistence;

use App\Modules\Content\Application\Interfaces\MetaTemplateRepositoryInterface;
use App\Modules\Content\Domain\Entities\MetaTemplateEntity;
use App\Modules\Content\Infrastructure\Models\MetaTemplate;

class MetaTemplateRepository implements MetaTemplateRepositoryInterface
{
    /** @return MetaTemplateEntity[] */
    public function getAll(): array
    {
        return MetaTemplate::all()
            ->map(fn(MetaTemplate $model) => $this->hydrate($model))
            ->toArray();
    }

    public function getById(int $id): MetaTemplateEntity
    {
        $model = MetaTemplate::findOrFail($id);
        return $this->hydrate($model);
    }

    public function getByClass(string $class): ?MetaTemplateEntity
    {
        $model = MetaTemplate::where('class', $class)->first();
        if ($model === null) {
            return null;
        }
        return $this->hydrate($model);
    }

    public function getByEntity(string $entity): ?MetaTemplateEntity
    {
        $model = MetaTemplate::where('entity', $entity)->first();
        if ($model === null) {
            return null;
        }
        return $this->hydrate($model);
    }

    public function existsByClass(string $class, ?int $excludeId = null): bool
    {
        $query = MetaTemplate::where('class', $class);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    public function existsByEntity(string $entity, ?int $excludeId = null): bool
    {
        $query = MetaTemplate::where('entity', $entity);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    public function save(MetaTemplateEntity $metaTemplate): MetaTemplateEntity
    {
        $model = $metaTemplate->id !== null
            ? MetaTemplate::findOrFail($metaTemplate->id)
            : new MetaTemplate();

        $model->class = $metaTemplate->class;
        $model->entity = $metaTemplate->entity;
        $model->template_title = $metaTemplate->templateTitle;
        $model->template_description = $metaTemplate->templateDescription;

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        $model = MetaTemplate::findOrFail($id);
        $model->delete();
    }

    private function hydrate(MetaTemplate $model): MetaTemplateEntity
    {
        $entity = new MetaTemplateEntity(
            class: $model->class,
            entity: $model->entity,
        );

        $entity->id = $model->id;
        $entity->templateTitle = $model->template_title ?? '';
        $entity->templateDescription = $model->template_description ?? '';

        return $entity;
    }
}
