<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\Actions\Feed;

use App\Modules\Output\Domain\Interfaces\FeedRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveFeedUseCase
{
    public function __construct(
        private FeedRepositoryInterface $feedRepository,
    ) {}

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('output.feed.delete')) {
            throw new AccessDeniedException();
        }

        $this->feedRepository->delete($id);
    }
}
