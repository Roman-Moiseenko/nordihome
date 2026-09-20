<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\Actions\Feed;

use App\Modules\Output\Application\DTOs\Feed\FeedIndexData;
use App\Modules\Output\Domain\Entities\FeedEntity;
use App\Modules\Output\Domain\Interfaces\FeedRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexFeedQuery
{
    public function __construct(
        private FeedRepositoryInterface $feedRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<int, FeedIndexData>
     */
    public function execute(UserPermission $userPermission, int $perPage = 15): LengthAwarePaginator
    {
        if (!$userPermission->can('output.feed.view'))
            throw new AccessDeniedException();


        return $this->feedRepository
            ->getAll($perPage)
            ->through(fn(FeedEntity $feed) => FeedIndexData::fromEntity($feed));
    }
}
