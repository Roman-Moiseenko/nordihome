<?php

declare(strict_types=1);

namespace App\Modules\Content\Infrastructure\Persistence;

use App\Modules\Content\Application\Interfaces\PostRepositoryInterface;
use App\Modules\Content\Domain\Entities\PostEntity;
use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use DateTimeImmutable;

class PostRepository implements PostRepositoryInterface
{
    /** @return PostEntity[] */
    public function getAll(): array
    {
        return Post::with(['image'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn(Post $model) => $this->hydrate($model))
            ->toArray();
    }

    public function getById(int $id): PostEntity
    {
        $model = Post::with(['image'])->findOrFail($id);
        return $this->hydrate($model);
    }

    public function save(PostEntity $post): PostEntity
    {
        $model = $post->id
            ? Post::findOrFail($post->id)
            : new Post();

        $model->name = $post->name;
        $model->slug = (string) $post->slug;
        $model->template = $post->template;
        $model->caption = $post->caption;
        $model->fragment = $post->fragment;
        $model->published = $post->isPublished();
        $model->category_id = $post->categoryId;
        $model->meta = $post->meta ? [
            'title' => $post->meta->getTitle(),
            'description' => $post->meta->getDescription(),
        ] : [];

        if ($post->publishedAt !== null) {
            $model->published_at = $post->publishedAt->format('Y-m-d H:i:s');
        }

        $model->save();

        return $this->hydrate($model->fresh()->load(['image']));
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
            template: $model->template,
            categoryId: $model->category_id,
        );

        $entity->id = $model->id;
        $entity->caption = $model->caption;
        $entity->fragment = $model->fragment;
        $entity->published = (bool) $model->published;

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
}
