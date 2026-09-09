<?php

namespace App\Modules\Content\Application\Actions\Label;

use App\Modules\Content\Domain\Entities\LabelEntity;
use App\Modules\Content\Domain\Interfaces\LabelRepositoryInterface;
use App\Modules\Shared\Application\DTOs\ListNameData;
use App\Modules\Shared\Application\DTOs\ListNamePublishedData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ListLabelUseCase
{
    public function __construct(
        private LabelRepositoryInterface $labelRepository,
    ) {}

    /** @return ListNameData[] */
    public function execute(): array
    {
        $entities = $this->labelRepository->getAll();

        return ListNameData::collect($entities);

    }
}
