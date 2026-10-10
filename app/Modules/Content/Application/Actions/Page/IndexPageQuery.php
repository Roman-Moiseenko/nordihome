<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Page;

use App\Modules\Content\Application\DTOs\Page\PageIndexData;
use App\Modules\Content\Domain\Entities\PageEntity;
use App\Modules\Content\Domain\Entities\PostEntity;
use App\Modules\Content\Domain\Interfaces\PageRepositoryInterface;

readonly class IndexPageQuery
{
    public function __construct(
        private PageRepositoryInterface $pageRepository,
    ) {}

    /** @return PostEntity[] */
    public function execute(): array
    {
        $pages = $this->pageRepository->getAll();

        return array_map(fn(PageEntity $page) => PageIndexData::fromEntity($page), $pages);
    }
}
