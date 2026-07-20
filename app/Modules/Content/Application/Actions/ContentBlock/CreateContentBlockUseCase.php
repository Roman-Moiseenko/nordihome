<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\ContentBlock;

use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockCreateData;
use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Domain\Entities\ContentBlockEntity;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Content\Domain\ValueObjects\ContentSection;

final readonly class CreateContentBlockUseCase
{
    public function __construct(
        private ContentBlockRepositoryInterface $contentBlockRepository,
    ) {}

    public function execute(ContentBlockCreateData $dto): ContentBlockEntity
    {
        $containerType = new ContainerType($dto->container_type);

        $block = new ContentBlockEntity(
            containerType: $containerType,
            containerId: $dto->container_id,
        );

        if ($dto->section !== null) {
            $block->section = new ContentSection($dto->section);
        }

        if ($dto->caption !== null) {
            $block->caption = $dto->caption;
        }

        return $this->contentBlockRepository->save($block);
    }
}
