<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Application\Services\Dadata\DadataService;
use App\Modules\Accounting\Application\Services\Organization\ApplyDadataPartyDataService;
use App\Modules\Accounting\Domain\Entities\OrganizationEntity;
use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RefreshOrganizationUseCase
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
        private DadataService $dadataService,
        private ApplyDadataPartyDataService $applyPartyDataService,
    ) {
    }

    public function execute(int $id, UserPermission $permission): OrganizationEntity
    {
        if (!$permission->can('accounting.organization.edit')) {
            throw new AccessDeniedException();
        }

        $organization = $this->organizationRepository->getById($id);

        if ($organization->isForeign()) {
            throw new \DomainException('Для иностранной компании данные не обновляются!');
        }

        $data = $this->dadataService->findPartyByInn($organization->inn);
        $this->applyPartyDataService->apply($organization, $data);

        return $this->organizationRepository->save($organization);
    }
}
