<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoParameter;

use App\Modules\Marketplace\Application\DTOs\AvitoParameter\AvitoParameterUpdateData;
use App\Modules\Marketplace\Domain\Entities\AvitoParameterEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateAvitoParameterUseCase
{
    public function __construct(
        private AvitoParameterRepositoryInterface $parameterRepository,
    ) {}

    public function execute(AvitoParameterUpdateData $dto, UserPermission $permission): AvitoParameterEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $parameter = $this->parameterRepository->getById($dto->id);
        $parameter->value = $dto->value;

        return $this->parameterRepository->save($parameter);
    }
}
