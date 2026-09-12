<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Visitor;

use App\Modules\Analytics\Domain\Entities\ActionEntity;
use App\Modules\Analytics\Domain\Entities\PageViewEntity;
use App\Modules\Analytics\Domain\Interfaces\ActionRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\PageViewRepositoryInterface;

/**
 * GetVisitorHistory — история посетителя: просмотры страниц и действия.
 */
final class GetVisitorHistoryQuery
{
    public function __construct(
        private readonly PageViewRepositoryInterface $pageViews,
        private readonly ActionRepositoryInterface $actions,
    ) {}

    /**
     * @return array{page_views: PageViewEntity[], actions: ActionEntity[]}
     */
    public function execute(int $visitorId, int $limit = 100, int $offset = 0): array
    {
        return [
            'page_views' => $this->pageViews->findByVisitor($visitorId, $limit, $offset),
            'actions' => $this->actions->findByVisitor($visitorId, $limit, $offset),
        ];
    }
}
