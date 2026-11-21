<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SearchOrganizationQuery
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
    ) {
    }

    /**
     * @return array<int, array{id: int, short_name: string, inn: string}>
     */
    public function execute(string $value, UserPermission $permission): array
    {
        if (!$permission->can('accounting.organization.view')) {
            throw new AccessDeniedException();
        }

        return $this->organizationRepository->search($value);
    }
}
