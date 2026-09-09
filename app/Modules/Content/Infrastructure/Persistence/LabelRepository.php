<?php

namespace App\Modules\Content\Infrastructure\Persistence;

use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Content\Infrastructure\Models\Label;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LabelRepository implements LabelRepositoryInterface
{
    /** @inheritDoc */
    public function getAll(): array
    {
        return Label::orderBy('name')
            ->get()
            ->map(fn(Label $model) => $this->hydrate($model))
            ->toArray();
    }

    /** @inheritDoc */
    public function findByIds(array $labelIds): array
    {
        return Label::whereIn('id', $labelIds)
            ->get()
            ->map(fn(Label $model) => $this->hydrate($model))
            ->toArray();
    }

    public function getById(int $labelId): LabelEntity
    {
        return $this->hydrate(Label::findOrFail($labelId));
    }

    public function save(LabelEntity $label): LabelEntity
    {
        $model = $label->id
            ? Label::findOrFail($label->id)
            : new Label();

        $model->name = $label->name;
        $model->slug = $label->slug->getValue();
        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function existsSlug(string $slug, ?int $excludeId = null): bool
    {
        $query = Label::where('slug', $slug);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Label::orderBy('name')
            ->paginate($perPage)
            ->through(fn(Label $model) => $this->hydrate($model));
    }

    public function findByName(string $name): ?LabelEntity
    {
        $model = Label::where('name', $name)->first();
        if (is_null($model)) {
            return null;
        }

        return $this->hydrate($model);
    }

    public function delete(int $labelId): void
    {
        Label::findOrFail($labelId)->delete();
    }

    private function hydrate(Label $model): LabelEntity
    {
        $label = new LabelEntity(
            name: $model->name,
            slug: new Slug($model->slug),
        );
        $label->id = $model->id;

        return $label;
    }
}
