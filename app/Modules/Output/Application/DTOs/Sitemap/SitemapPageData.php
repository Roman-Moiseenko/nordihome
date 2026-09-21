<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\Sitemap;

use Spatie\LaravelData\Data;

/**
 * DTO одной записи карты сайта (sitemap.xml).
 */
class SitemapPageData extends Data
{
    public function __construct(
        public readonly string $url,
        public readonly string $date,
        public readonly string $changefreq,
    ) {}
}
