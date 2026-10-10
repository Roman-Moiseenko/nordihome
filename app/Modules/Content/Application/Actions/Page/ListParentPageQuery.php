<?php

namespace App\Modules\Content\Application\Actions\Page;

use App\Modules\Content\Domain\Interfaces\PageRepositoryInterface;
use App\Modules\Shared\Application\DTOs\ListNamePublishedData;

readonly class ListParentPageQuery
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    )
    {
    }
    /**
     * @return array
     */
    public function execute(): array
    {
        $pages = $this->pageRepository->getRootAll();

        return array_map(fn($page) => new ListNamePublishedData(
            id: $page->id,
            name: $page->name,
            published: $page->published,
        ), $pages);
    }
}
