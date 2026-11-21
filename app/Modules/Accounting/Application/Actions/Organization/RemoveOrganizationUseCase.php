<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveOrganizationUseCase
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
    ) {
    }

    public function execute(int $id, UserPermission $permission): void
    {
        if (!$permission->can('accounting.organization.delete')) {
            throw new AccessDeniedException();
        }

        $this->organizationRepository->delete($id);
    }
}
