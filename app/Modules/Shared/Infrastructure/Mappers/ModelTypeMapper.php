<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Mappers;

/**
 * Маппинг model_type ({модуль}.{сущность}) в полное имя класса (FQCN) для imageable_type
 */
class ModelTypeMapper
{
    /**
     * model_type, для которых миниатюры (thumbs) не создаются.
     * Используется, чтобы пропускать их при массовом создании/пересоздании кэша.
     */
    public const array NO_THUMBS = [
        'marketplace.avito-product',
        'marketplace.avito-common-image',
    ];

    private const array MAP = [
        'catalog.room' => \App\Modules\Catalog\Infrastructure\Models\Room::class,
        'catalog.category' => \App\Modules\Catalog\Infrastructure\Models\Category::class,
        'catalog.product' => \App\Modules\Catalog\Infrastructure\Models\Product::class,
        'catalog.brand' => \App\Modules\Catalog\Infrastructure\Models\Brand::class,
        'catalog.group' => \App\Modules\Catalog\Infrastructure\Models\Group::class,
        'catalog.tag' => \App\Modules\Catalog\Infrastructure\Models\Tag::class,
        'catalog.attribute-variant' => \App\Modules\Catalog\Infrastructure\Models\AttributeVariant::class,
        'catalog.attribute' => \App\Modules\Catalog\Infrastructure\Models\Attribute::class,
        'catalog.attribute-group' => \App\Modules\Catalog\Infrastructure\Models\AttributeGroup::class,
        'catalog.review' => \App\Modules\Catalog\Infrastructure\Models\Review::class,

        'parser.category' => \App\Modules\Parser\Infrastructure\Models\ParserCategory::class,
        'parser.product' => \App\Modules\Parser\Infrastructure\Models\ParserProduct::class,

        'content.post-category' => \App\Modules\Content\Infrastructure\Models\PostCategory::class,
        'content.post' => \App\Modules\Content\Infrastructure\Models\Post::class,
        'content.page' => \App\Modules\Content\Infrastructure\Models\Page::class,
        'content.banner-widget-item' => \App\Modules\Content\Entity\Widgets\BannerWidgetItem::class,

        'content.gallery' => \App\Modules\Content\Infrastructure\Models\Gallery::class, //MAINDO Перенести и в Базе данных, также добавиль model_type
        'discount.promotion' => \App\Modules\Discount\Infrastructure\Models\Promotion::class,

        'marketplace.avito-product' => \App\Modules\Marketplace\Infrastructure\Models\AvitoProduct::class,
        'marketplace.avito-common-image' => \App\Modules\Marketplace\Infrastructure\Models\AvitoCommonImage::class,

    ];

    public static function toFqcn(string $modelType): string
    {
        return self::MAP[$modelType]
            ?? throw new \InvalidArgumentException("Неизвестный model_type: {$modelType}");
    }

    /** @return list<string> */
    public static function allTypes(): array
    {
        return array_keys(self::MAP);
    }
}
