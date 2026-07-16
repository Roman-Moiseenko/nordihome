<?php

namespace App\Modules\Content\Application\Actions\ContentBlock;

use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockContainerData;
use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Domain\Entities\ContentBlockEntity;

readonly class ListContentBlockByContainerUseCase
{

    public function __construct(
        private ContentBlockRepositoryInterface $repository
    ) {
    }

    /**
     * @param ContentBlockContainerData $dto
     * @return ContentBlockEntity[]
     */
    public function execute(ContentBlockContainerData $dto): array
    {
        return $this->repository->getByContainer(
            containerType: $dto->containerType,
            containerId: $dto->containerId,
        );
    }
}
