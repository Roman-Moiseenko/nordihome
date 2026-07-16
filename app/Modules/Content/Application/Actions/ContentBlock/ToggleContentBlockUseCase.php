<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\ContentBlock;

use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Domain\Entities\ContentBlockEntity;

final readonly class ToggleContentBlockUseCase
{
    public function __construct(
        private ContentBlockRepositoryInterface $contentBlockRepository,
    ) {}

    public function execute(int $id): ContentBlockEntity
    {
        $block = $this->contentBlockRepository->getById($id);

        $block->active = !$block->active;

        return $this->contentBlockRepository->save($block);
    }
}
