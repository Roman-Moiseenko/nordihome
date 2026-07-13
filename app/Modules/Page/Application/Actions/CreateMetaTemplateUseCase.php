<?php

namespace App\Modules\Page\Application\Actions;

use App\Modules\Page\Application\DTOs\MetaTemplateCreateData;
use App\Modules\Page\Application\Interfaces\MetaTemplateRepositoryInterface;
use App\Modules\Page\Domain\Entities\MetaTemplateEntity;

readonly class CreateMetaTemplateUseCase
{
    public function __construct(
        private MetaTemplateRepositoryInterface $metaTemplateRepository,
    )
    {
    }

    public function execute(MetaTemplateCreateData $dto): void
    {
        if ($this->metaTemplateRepository->existsByClass($dto->class)) return;
        if ($dto->entity !== null && $this->metaTemplateRepository->existsByEntity($dto->entity)) return;

        $metaTemplate = new MetaTemplateEntity(
            class: $dto->class,
            entity: $dto->entity,
        );

        $this->metaTemplateRepository->save($metaTemplate);
    }
}

