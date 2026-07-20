<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\ContentBlock;

use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockContainerData;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockViewData;
use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;

readonly class ListContentBlockByContainerUseCase
{

    public function __construct(
        private ContentBlockRepositoryInterface $repository
    ) {
    }

    /**
     * @param ContentBlockContainerData $dto
     * @return ContentBlockViewData[]
     */
    public function execute(ContentBlockContainerData $dto): array
    {
        $blocks = $this->repository->getByContainer(
            containerType: $dto->containerType,
            containerId: $dto->containerId,
        );

        return array_map(
            fn($block) => ContentBlockViewData::fromEntity($block),
            $blocks,
        );
    }
}
