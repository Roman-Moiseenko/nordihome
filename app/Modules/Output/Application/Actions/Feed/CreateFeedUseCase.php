<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\Actions\Feed;

use App\Modules\Output\Application\DTOs\Feed\FeedCreateData;
use App\Modules\Output\Domain\Entities\FeedEntity;
use App\Modules\Output\Domain\Interfaces\FeedRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateFeedUseCase
{
    public function __construct(
        private FeedRepositoryInterface $feedRepository,
    ) {}

    public function execute(FeedCreateData $dto, UserPermission $userPermission): FeedEntity
    {
        if (!$userPermission->can('output.feed.create')) {
            throw new AccessDeniedException();
        }

        $feed = new FeedEntity(name: $dto->name);

        return $this->feedRepository->save($feed);
    }
}
