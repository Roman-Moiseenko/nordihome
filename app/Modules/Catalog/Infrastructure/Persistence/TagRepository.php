<?php

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Catalog\Infrastructure\Models\Tag;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TagRepository implements TagRepositoryInterface
{

    public function getById(int $tagId): TagEntity
    {
        $tag = Tag::findOrFail($tagId);
        return $this->hydrate($tag);
    }
    public function save(TagEntity $tag): TagEntity
    {
        $model = $tag->id
            ? Tag::findOrFail($tag->id)
            : new Tag();

        $model->name = $tag->name;
        $model->slug = $tag->slug->getValue();

        $model->save();

        return $this->hydrate($model->fresh());
    }

    public function existsSlug(string $slug): bool
    {
        return Tag::where('slug', $slug)->exists();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Tag::orderBy('name')
            ->paginate($perPage)
            ->through(fn(Tag $model) => $this->hydrate($model));
    }

    public function findByName(string $name):? TagEntity
    {
        $model = Tag::where('name', $name)->first();
        if (is_null($model)) return null;
        return $this->hydrate($model);
    }

    public function delete(int $tagId)
    {
        $model = Tag::findOrFail($tagId);
        $model->delete();
    }

    private function hydrate(Tag $model): TagEntity
    {
        $tag = new TagEntity(
            name: $model->name,
            slug: new Slug($model->slug),
        );
        $tag->id = $model->id;
        $tag->image_url = $model->getImage();

        return $tag;
    }


}
