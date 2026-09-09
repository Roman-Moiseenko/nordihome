<?php

declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Persistence;

use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Entities\PostEntity;
use App\Modules\Content\Domain\Interfaces\PostRepositoryInterface;
use App\Modules\Content\Infrastructure\Models\Label;
use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use DateTimeImmutable;

class PostRepository implements PostRepositoryInterface
{
    /** @return PostEntity[] */
    public function getAll(): array
    {
        return Post::with(['image', 'labels'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn(Post $model) => $this->hydrate($model))
            ->toArray();
    }

    public function getById(int $id): PostEntity
    {
        $model = Post::with(['image', 'labels'])->findOrFail($id);
        return $this->hydrate($model);
    }

    public function save(PostEntity $post): PostEntity
    {
        $model = $post->id
            ? Post::findOrFail($post->id)
            : new Post();

        $model->name = $post->name;
        $model->slug = (string) $post->slug;
        $model->caption = $post->caption ?? $post->name;
        $model->fragment = $post->fragment;
        $model->text = $post->text ?? '';
        $model->published = $post->isPublished();
        $model->category_id = $post->categoryId;
        $model->old_render = $post->oldRender;
        $model->meta = $post->meta ? [
            'title' => $post->meta->getTitle(),
            'description' => $post->meta->getDescription(),
        ] : [];

        if ($post->publishedAt !== null) {
            $model->published_at = $post->publishedAt->format('Y-m-d H:i:s');
        }

        $model->save();

        $labelIds = array_values(array_unique(array_map(
            static fn (LabelEntity $label) => $label->id,
            $post->labels,
        )));
        $model->labels()->sync($labelIds);

        return $this->hydrate($model->fresh()->load(['image', 'labels']));
    }

    public function delete(int $id): void
    {
        $model = Post::findOrFail($id);
        $model->delete();
    }

    public function existsSlug(string $slug, ?int $excludeId = null): bool
    {
        $query = Post::where('slug', $slug);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    private function hydrate(Post $model): PostEntity
    {
        $entity = new PostEntity(
            name: $model->name,
            slug: new Slug($model->slug),
            categoryId: $model->category_id,
        );

        $entity->id = $model->id;
        $entity->caption = $model->caption;
        $entity->fragment = $model->fragment;
        $entity->published = $model->published;
        $entity->oldRender = $model->old_render;
        $entity->text = $model->text;
        $entity->labels = $model->labels
            ->map(fn (Label $label) => $this->hydrateLabel($label))
            ->all();

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
        $metaData = is_array($model->meta) ? $model->meta : [];
        $entity->meta = new Meta(
            title: $metaData['title'] ?? '',
            description: $metaData['description'] ?? '',
        );

        return $entity;
    }

    private function hydrateLabel(Label $model): LabelEntity
    {
        $label = new LabelEntity(
            name: $model->name,
            slug: new Slug($model->slug),
        );
        $label->id = $model->id;

        return $label;
    }
}
