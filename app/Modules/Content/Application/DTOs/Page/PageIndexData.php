<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\Page;

use App\Modules\Content\Domain\Entities\PageEntity;
use Spatie\LaravelData\Data;

class PageIndexData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly bool $published,
        public readonly ?string $template,
        public readonly ?string $publishedAt,
        public readonly ?int $parentId,
        public readonly ?bool $oldRender = false,
    ) {}

    public static function fromEntity(PageEntity $page): self
    {
        return new self(
            id: $page->id,
            name: $page->name,
            slug: (string) $page->slug,
            published: $page->isPublished(),
            template: $page->template,
            publishedAt: $page->publishedAt?->format('Y-m-d H:i:s'),
            parentId: $page->parentId,
            oldRender: $page->oldRender,
        );
    }
}
