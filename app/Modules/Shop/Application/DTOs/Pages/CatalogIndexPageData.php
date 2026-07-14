<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Domain\Schema\SchemaData;

/**
 * Для страниц каталога - shop.catalog.category и shop.catalog.room
 */
readonly class CatalogIndexPageData
{

    public function __construct(
        public SeoData $meta,
        /** @var CategoryRoomData[] $categories */
        public array   $categories,
        public SchemaData           $schema,
    )
    {
    }
}
