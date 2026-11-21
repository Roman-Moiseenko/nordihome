<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Application\DTOs\Organization\OrganizationViewData;
use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Guide\Entity\VAT;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewOrganizationQuery
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
    ) {
    }

    public function execute(int $id, UserPermission $permission): OrganizationViewData
    {
        if (!$permission->can('accounting.organization.view')) {
            throw new AccessDeniedException();
        }

        $organization = $this->organizationRepository->getById($id);

        $vatCaption = 'Без НДС';
        if ($organization->vatId !== null) {
            $vatCaption = (string) (VAT::find($organization->vatId)?->name ?? 'Без НДС');
        }

        return OrganizationViewData::fromEntity(
            organization: $organization,
            contracts: $this->organizationRepository->getContracts($id),
            documents: $this->organizationRepository->getDocuments($id),
            trader: $this->organizationRepository->getTraderLink($id),
            distributor: $this->organizationRepository->getDistributorLink($id),
            shopper: $this->organizationRepository->getShopperLink($id),
            vatCaption: $vatCaption,
        );
    }
}
