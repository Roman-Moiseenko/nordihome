<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Application\DTOs\Organization\OrganizationCreateData;
use App\Modules\Accounting\Domain\Entities\OrganizationEntity;
use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateForeignOrganizationUseCase
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
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

        $name = trim((string) ($dto->name ?? '')) !== '' ? trim($dto->name) : $dto->inn;

        $organization = new OrganizationEntity(
            fullName: $name,
            shortName: $name,
            inn: $dto->inn,
        );
        $organization->bik = trim((string) $dto->bik);
        $organization->bankName = trim((string) $dto->bank);
        $organization->payAccount = trim((string) $dto->account);
        $organization->foreign = true;

        return $this->organizationRepository->save($organization);
    }
}
