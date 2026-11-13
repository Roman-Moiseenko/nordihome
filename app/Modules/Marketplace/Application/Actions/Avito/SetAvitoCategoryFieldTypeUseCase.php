<?php

namespace App\Modules\Marketplace\Application\Actions\Avito;

use App\Modules\Marketplace\Domain\Entities\AvitoCategoryFieldEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SetAvitoCategoryFieldTypeUseCase
{
    public function __construct(
        private AvitoCategoryFieldRepositoryInterface $fieldRepository,
    ) {}

    public function execute(int $fieldId, ?string $type, UserPermission $permission): AvitoCategoryFieldEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $field = $this->fieldRepository->getById($fieldId);
        $field->type = $type;

        return $this->fieldRepository->save($field);
    }
}
