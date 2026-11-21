<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class DeleteOrganizationContactUseCase
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
    ) {
    }

    public function execute(int $contactId, UserPermission $permission): void
    {
        if (!$permission->can('accounting.organization.edit')) {
            throw new AccessDeniedException();
        }

        $this->organizationRepository->deleteContact($contactId);
    }
}
