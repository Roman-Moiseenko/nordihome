<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\Queries\Sitemap;

use App\Modules\Output\Application\DTOs\Sitemap\SitemapPageData;
use App\Modules\Output\Infrastructure\Persistence\Query\SitemapQueryRepository;

readonly class GetSitemapQuery
{
    public function __construct(
        private SitemapQueryRepository $repository,
    ) {}

    /**
     * @return SitemapPageData[]
     */
    public function execute(): array
    {
        return array_map(
            static fn (array $page) => SitemapPageData::from($page),
            $this->repository->getCachedPages(),
        );
    }
}
