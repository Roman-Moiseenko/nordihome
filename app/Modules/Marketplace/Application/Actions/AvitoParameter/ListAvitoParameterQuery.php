<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoParameter;

use App\Modules\Marketplace\Domain\Entities\AvitoParameterEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ListAvitoParameterQuery
{
    public function __construct(
        private AvitoParameterRepositoryInterface $parameterRepository,
    ) {}

    /**
     * @return AvitoParameterEntity[]
     */
    public function execute(UserPermission $permission): array
    {
        if (!$permission->can('marketplace.avito.view')) {
            throw new AccessDeniedException();
        }

        return $this->parameterRepository->getAll();
    }
}
