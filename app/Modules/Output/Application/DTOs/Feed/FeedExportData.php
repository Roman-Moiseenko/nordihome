<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\Feed;

use Spatie\LaravelData\Data;

/**
 * Единый DTO выгрузки фида (Google Merchant / Yandex Market).
 */
class FeedExportData extends Data
{
    public function __construct(
        public readonly FeedInfoData $info,
        /** @var FeedProductData[] */
        public readonly array $products,
        /** @var FeedCategoryData[] */
        public readonly array $categories,
    ) {}
}
