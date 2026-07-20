<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\ContentBlock;

use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockUpdateData;
use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Domain\Entities\ContentBlockEntity;
use App\Modules\Content\Domain\ValueObjects\ContentSection;

final readonly class UpdateContentBlockUseCase
{
    public function __construct(
        private ContentBlockRepositoryInterface $contentBlockRepository,
    ) {}

    public function execute(int $id, ContentBlockUpdateData $dto): ContentBlockEntity
    {
        $block = $this->contentBlockRepository->getById($id);

        if ($dto->caption !== null) {
            $block->caption = $dto->caption;
        }

        if ($dto->section !== null) {
            $block->section = new ContentSection($dto->section);
        }

        return $this->contentBlockRepository->save($block);
    }
}
