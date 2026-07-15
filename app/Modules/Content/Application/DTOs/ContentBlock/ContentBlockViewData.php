<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\DTOs\ContentBlock;

use App\Modules\Content\Application\DTOs\WidgetInstance\WidgetInstanceViewData;
use App\Modules\Content\Domain\Entities\ContentBlockEntity;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class ContentBlockViewData extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public readonly int $id,
        public readonly string $containerType,
        public readonly int $containerId,
        public readonly ?int $widgetInstanceId,
        public readonly ?int $sort,
        public readonly ?string $section,
        public readonly ?string $caption,
        public readonly ?WidgetInstanceViewData $widgetInstance = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromEntity(ContentBlockEntity $block): self
    {
        return new self(
            id: $block->id,
            containerType: (string) $block->containerType,
            containerId: $block->containerId,
            widgetInstanceId: $block->widgetInstanceId,
            sort: $block->sort,
            section: $block->section,
            caption: $block->caption,
            widgetInstance: $block->widgetInstance !== null
                ? WidgetInstanceViewData::fromEntity($block->widgetInstance)
                : null,
            createdAt: $block->createdAt?->format('c'),
            updatedAt: $block->updatedAt?->format('c'),
        );
    }
}
