<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\ContentBlock;

use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;
use App\Modules\Content\Domain\Entities\ContentBlockEntity;

final readonly class ViewContentBlockUseCase
{
    public function __construct(
        private ContentBlockRepositoryInterface $contentBlockRepository,
    ) {}

    public function execute(int $id): ContentBlockEntity
    {
        return $this->contentBlockRepository->getById($id);
    }
}
