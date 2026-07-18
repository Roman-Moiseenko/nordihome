<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Post;

use App\Modules\Content\Domain\Entities\PostEntity;
use Spatie\LaravelData\Data;

class PostViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $caption,
        public readonly ?string $fragment,
        public readonly string $template,
        public readonly bool $published,
        public readonly ?string $publishedAt,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?array $meta,
        public readonly ?int $categoryId,
    ) {}

    public static function fromEntity(PostEntity $post): self
    {
        return new self(
            id: $post->id,
            name: $post->name,
            slug: (string) $post->slug,
            caption: $post->caption,
            fragment: $post->fragment,
            template: $post->template,
            published: $post->isPublished(),
            publishedAt: $post->publishedAt?->format('Y-m-d H:i:s'),
            createdAt: $post->createdAt?->format('Y-m-d H:i:s'),
            updatedAt: $post->updatedAt?->format('Y-m-d H:i:s'),
            meta: $post->meta ? [
                'title' => $post->meta->getTitle(),
                'description' => $post->meta->getDescription(),
            ] : null,
            categoryId: $post->categoryId,
        );
    }
}
