<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Search;

use App\Modules\Analytics\Domain\Entities\SearchEntity;
use App\Modules\Analytics\Domain\Interfaces\SearchRepositoryInterface;

/**
 * GetVisitorSearches — история поисковых запросов посетителя.
 */
final class GetVisitorSearchesQuery
{
    public function __construct(
        private readonly SearchRepositoryInterface $searches,
    ) {}

    /**
     * @return SearchEntity[]
     */
    public function execute(int $visitorId, int $limit = 50, int $offset = 0): array
    {
        return $this->searches->findByVisitor($visitorId, $limit, $offset);
    }
}
