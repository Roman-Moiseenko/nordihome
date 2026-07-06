<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure\Mappers;

/**
 * Маппинг model_type ({модуль}.{сущность}) в полное имя класса (FQCN) для imageable_type
 */
class ModelTypeMapper
{
    private const array MAP = [
        'catalog.room' => \App\Modules\Catalog\Infrastructure\Models\Room::class,
        'catalog.category' => \App\Modules\Catalog\Infrastructure\Models\Category::class,
        'catalog.product' => \App\Modules\Catalog\Infrastructure\Models\Product::class,
        'parser.category' => \App\Modules\Parser\Infrastructure\Models\ParserCategory::class,
        'parser.product' => \App\Modules\Parser\Infrastructure\Models\ParserProduct::class,
    ];

    public static function toFqcn(string $modelType): string
    {
        return self::MAP[$modelType]
            ?? throw new \InvalidArgumentException("Неизвестный model_type: {$modelType}");
    }
}
