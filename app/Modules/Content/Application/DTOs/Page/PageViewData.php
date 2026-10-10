<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Page;

use App\Modules\Content\Domain\Entities\PageEntity;
use Spatie\LaravelData\Data;

class PageViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $svg,
        public readonly ?string $text,
        public readonly ?string $template,
        public readonly int $sort,
        public readonly bool $published,
        public readonly ?string $publishedAt,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?array $meta,
        public readonly ?int $parentId,
        public readonly bool $oldRender,
    ) {}

    public static function fromEntity(PageEntity $page): self
    {
        return new self(
            id: $page->id,
            name: $page->name,
            slug: (string) $page->slug,
            svg: $page->svg,
            text: $page->text,
            template: $page->template,
            sort: $page->sort ?? 0,
            published: $page->isPublished(),
            publishedAt: $page->publishedAt?->format('Y-m-d H:i:s'),
            createdAt: $page->createdAt?->format('Y-m-d H:i:s'),
            updatedAt: $page->updatedAt?->format('Y-m-d H:i:s'),
            meta: $page->meta ? [
                'title' => $page->meta->getTitle(),
                'description' => $page->meta->getDescription(),
            ] : null,
            parentId: $page->parentId,
            oldRender: $page->oldRender,
        );
    }
}
