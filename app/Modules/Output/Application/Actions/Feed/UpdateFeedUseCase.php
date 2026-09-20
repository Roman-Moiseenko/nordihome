<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\Actions\Feed;

use App\Modules\Output\Application\DTOs\Feed\FeedUpdateData;
use App\Modules\Output\Domain\Entities\FeedEntity;
use App\Modules\Output\Domain\Interfaces\FeedRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateFeedUseCase
{
    public function __construct(
        private FeedRepositoryInterface $feedRepository,
    ) {}

    public function execute(int $id, FeedUpdateData $dto, UserPermission $userPermission): FeedEntity
    {
        if (!$userPermission->can('output.feed.edit')) {
            throw new AccessDeniedException();
        }

        $feed = $this->feedRepository->getById($id);

        if ($dto->name !== null) {
            $feed->name = $dto->name;
        }

        if ($dto->active !== null) {
            $feed->active = $dto->active;
        }

        if ($dto->productsIn !== null) {
            $feed->productsIn = $dto->productsIn;
        }

        if ($dto->productsOut !== null) {
            $feed->productsOut = $dto->productsOut;
        }

        if ($dto->categoriesIn !== null) {
            $feed->categoriesIn = $dto->categoriesIn;
        }

        if ($dto->categoriesOut !== null) {
            $feed->categoriesOut = $dto->categoriesOut;
        }

        if ($dto->roomsIn !== null) {
            $feed->roomsIn = $dto->roomsIn;
        }

        if ($dto->roomsOut !== null) {
            $feed->roomsOut = $dto->roomsOut;
        }

        if ($dto->promotionsIn !== null) {
            $feed->promotionsIn = $dto->promotionsIn;
        }

        if ($dto->promotionsOut !== null) {
            $feed->promotionsOut = $dto->promotionsOut;
        }

        if ($dto->groupsIn !== null) {
            $feed->groupsIn = $dto->groupsIn;
        }

        if ($dto->groupsOut !== null) {
            $feed->groupsOut = $dto->groupsOut;
        }

        if ($dto->tagsIn !== null) {
            $feed->tagsIn = $dto->tagsIn;
        }

        if ($dto->tagsOut !== null) {
            $feed->tagsOut = $dto->tagsOut;
        }

        if ($dto->setPreprice !== null) {
            $feed->setPreprice = $dto->setPreprice;
        }

        if ($dto->setTitle !== null) {
            $feed->setTitle = $dto->setTitle;
        }

        if ($dto->setDescription !== null) {
            $feed->setDescription = $dto->setDescription;
        }

        return $this->feedRepository->save($feed);
    }
}
