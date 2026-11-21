<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Application\DTOs\Organization\OrganizationCreateData;
use App\Modules\Accounting\Application\Services\Dadata\DadataService;
use App\Modules\Accounting\Application\Services\Organization\ApplyDadataPartyDataService;
use App\Modules\Accounting\Domain\Entities\OrganizationEntity;
use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class FindOrganizationByInnUseCase
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
        private DadataService $dadataService,
        private ApplyDadataPartyDataService $applyPartyDataService,
    ) {
    }

    public function execute(OrganizationCreateData $dto, UserPermission $permission): OrganizationEntity
    {
        if (!$permission->can('accounting.organization.create')) {
            throw new AccessDeniedException();
        }

        $existing = $this->organizationRepository->findByInn($dto->inn);
        if ($existing !== null) {
            return $existing;
        }

        $data = $this->dadataService->findPartyByInn($dto->inn);

        $organization = new OrganizationEntity(
            fullName: (string) ($data['name']['full_with_opf'] ?? $dto->inn),
            shortName: (string) ($data['name']['short_with_opf'] ?? $dto->inn),
            inn: $dto->inn,
        );

        $this->applyPartyDataService->apply($organization, $data);

        if (!empty($dto->bik)) {
            $bank = $this->dadataService->findBankByBik($dto->bik);
            if (!empty($bank)) {
                $this->applyPartyDataService->applyBank($organization, $bank);
            }
        }

        $organization->payAccount = trim((string) $dto->account);

        return $this->organizationRepository->save($organization);
    }
}
