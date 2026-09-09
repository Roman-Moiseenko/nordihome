<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Post;

use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Entities\PostEntity;
use App\Modules\Shared\Application\DTOs\ListNameData;
use Spatie\LaravelData\Data;

class PostViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $caption,
        public readonly ?string $fragment,
        public readonly bool $published,
        public readonly ?string $publishedAt,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?array $meta,
        public readonly ?int $categoryId,
        public readonly ?string $text,
        /** @var ListNameData[] */
        public readonly array $labels = [],
        public readonly bool $oldRender = false,
    ) {}

    public static function fromEntity(PostEntity $post): self
    {
        return new self(
            id: $post->id,
            name: $post->name,
            slug: (string) $post->slug,
            caption: $post->caption,
            fragment: $post->fragment,
            published: $post->isPublished(),
            publishedAt: $post->publishedAt?->format('Y-m-d H:i:s'),
            createdAt: $post->createdAt?->format('Y-m-d H:i:s'),
            updatedAt: $post->updatedAt?->format('Y-m-d H:i:s'),
            meta: $post->meta ? [
                'title' => $post->meta->getTitle(),
                'description' => $post->meta->getDescription(),
            ] : null,
            categoryId: $post->categoryId,
            text: $post->text,
            labels: array_map(
                static fn (LabelEntity $label) => new ListNameData((int) $label->id, $label->name),
                $post->labels,
            ),
            oldRender: $post->oldRender,
        );
    }
}
