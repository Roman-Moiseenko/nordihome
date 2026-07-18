<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Post;

use App\Modules\Content\Domain\Entities\PostEntity;
use Spatie\LaravelData\Data;

class PostIndexData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly bool $published,
        public readonly ?string $publishedAt,
        public readonly ?int $categoryId,
    ) {}

    public static function fromEntity(PostEntity $post): self
    {
        return new self(
            id: $post->id,
            name: $post->name,
            slug: (string) $post->slug,
            published: $post->isPublished(),
            publishedAt: $post->publishedAt?->format('Y-m-d H:i:s'),
            categoryId: $post->categoryId,
        );
    }
}
