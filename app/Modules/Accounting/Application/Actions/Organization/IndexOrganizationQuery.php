<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Application\DTOs\Organization\FilterOrganizationIndexData;
use App\Modules\Accounting\Application\DTOs\Organization\OrganizationIndexData;
use App\Modules\Accounting\Domain\Entities\OrganizationEntity;
use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class IndexOrganizationQuery
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
    ) {
    }

    public function execute(FilterOrganizationIndexData &$filter, UserPermission $permission): LengthAwarePaginator
    {
        if (!$permission->can('accounting.organization.view')) {
            throw new AccessDeniedException();
        }

        $paginator = $this->organizationRepository->filterPaginate($filter);

        return $paginator->through(
            fn(OrganizationEntity $organization) => OrganizationIndexData::fromEntity(
                $organization,
                $this->organizationRepository->getTypes($organization->id)
            )
        );
    }
}
