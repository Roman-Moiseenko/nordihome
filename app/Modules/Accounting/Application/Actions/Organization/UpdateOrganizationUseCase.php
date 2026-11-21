<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Application\DTOs\Organization\OrganizationUpdateData;
use App\Modules\Accounting\Application\Services\Dadata\DadataService;
use App\Modules\Accounting\Application\Services\Organization\ApplyDadataPartyDataService;
use App\Modules\Accounting\Domain\Entities\OrganizationEntity;
use App\Modules\Accounting\Domain\Interfaces\OrganizationRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\Address;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\PhoneNumber;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateOrganizationUseCase
{
    public function __construct(
        private OrganizationRepositoryInterface $organizationRepository,
        private DadataService $dadataService,
        private ApplyDadataPartyDataService $applyPartyDataService,
    ) {
    }

    public function execute(int $id, OrganizationUpdateData $dto, UserPermission $permission): OrganizationEntity
    {
        if (!$permission->can('accounting.organization.edit')) {
            throw new AccessDeniedException();
        }

        $organization = $this->organizationRepository->getById($id);

        if ($dto->fullName !== null) $organization->fullName = $dto->fullName;
        if ($dto->shortName !== null) $organization->shortName = $dto->shortName;
        if ($dto->post !== null) $organization->post = $dto->post;
        if ($dto->payAccount !== null) $organization->payAccount = $dto->payAccount;
        if ($dto->bank !== null) $organization->bankName = $dto->bank;
        if ($dto->email !== null) {
            $organization->email = trim($dto->email) !== '' ? new Email($dto->email) : null;
        }
        if ($dto->phone !== null) $organization->phone = new PhoneNumber($dto->phone);
        if ($dto->chief !== null) $organization->chief = new FullName($dto->chief);

        if ($dto->bik !== null) {
            $bik = trim($dto->bik);
            if ($organization->isForeign()) {
                $organization->bik = $bik;
            } elseif ($bik !== '') {
                $bank = $this->dadataService->findBankByBik($bik);
                if (empty($bank)) {
                    throw new \DomainException('Неверный БИК');
                }
                $this->applyPartyDataService->applyBank($organization, $bank);
            }
        }

        if ($dto->address !== null) {
            $organization->actualAddress = new Address(
                country: $dto->address['country'] ?? null,
                city: $dto->address['city'] ?? null,
                street: $dto->address['street'] ?? null,
                region: $dto->address['region'] ?? null,
                postalCode: $dto->address['postalCode'] ?? null,
                house: $dto->address['house'] ?? null,
                building: $dto->address['building'] ?? null,
                flat: $dto->address['flat'] ?? null,
            );
        }

        if ($dto->vatId !== null && $organization->vatId !== $dto->vatId) {
            $organization->vatId = $dto->vatId;
            if ($dto->vatAll) {
                Product::where('id', '>', 0)->update(['vat_id' => $dto->vatId]);
            }
        }

        return $this->organizationRepository->save($organization);
    }
}
