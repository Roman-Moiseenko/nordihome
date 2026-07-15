<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\ContentBlock;

use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockSortData;
use App\Modules\Content\Application\Interfaces\ContentBlockRepositoryInterface;

final readonly class SortContentBlockUseCase
{
    public function __construct(
        private ContentBlockRepositoryInterface $contentBlockRepository,
    ) {}

    /**
     * Сортирует блоки. Блок с указанным id получает новый sort,
     * остальные блоки в том же контейнере пересчитываются.
     */
    public function execute(ContentBlockSortData $dto): void
    {
        $this->contentBlockRepository->updateSortOrder($dto->id, $dto->sort);
    }
}
