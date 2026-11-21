<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Application\DTOs\Organization\OrganizationContactData;
use App\Modules\Accounting\Domain\Entities\OrganizationContactEntity;
use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SyncOrganizationContactUseCase
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
    ) {
    }

    public function execute(int $id, OrganizationContactData $dto, UserPermission $permission): OrganizationContactEntity
    {
        if (!$permission->can('accounting.organization.edit')) {
            throw new AccessDeniedException();
        }

        return $this->organizationRepository->saveContact($id, $dto);
    }
}
