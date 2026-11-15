<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoParameterRegistry;

use App\Modules\Marketplace\Domain\Entities\AvitoParameterRegistryEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRegistryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ListAvitoParameterRegistryQuery
{
    public function __construct(
        private AvitoParameterRegistryRepositoryInterface $registryRepository,
    ) {}

    /**
     * @return AvitoParameterRegistryEntity[]
     */
    public function execute(UserPermission $permission): array
    {
        if (!$permission->can('marketplace.avito.view')) {
            throw new AccessDeniedException();
        }

        return $this->registryRepository->getAll();
    }
}
