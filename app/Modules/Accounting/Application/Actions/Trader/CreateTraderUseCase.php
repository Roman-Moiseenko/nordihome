<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Trader;

use App\Modules\Accounting\Application\Actions\Organization\FindOrganizationByInnUseCase;
use App\Modules\Accounting\Application\DTOs\Organization\OrganizationCreateData;
use App\Modules\Accounting\Application\DTOs\Trader\TraderCreateData;
use App\Modules\Accounting\Domain\Entities\TraderEntity;
use App\Modules\Accounting\Domain\Interfaces\TraderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateTraderUseCase
{
    public function __construct(
        private TraderRepositoryInterface $traderRepository,
        private FindOrganizationByInnUseCase $findOrganizationByInnUseCase,
    ) {
    }

    public function execute(TraderCreateData $dto, UserPermission $permission): TraderEntity
    {
        if (!$permission->can('accounting.trader.create')) {
            throw new AccessDeniedException();
        }

        $trader = new TraderEntity($dto->name);
        $trader = $this->traderRepository->save($trader);

        $organization = $this->findOrganizationByInnUseCase->execute(
            new OrganizationCreateData(
                inn: $dto->inn,
                bik: $dto->bik,
                account: $dto->account,
            ),
            $permission
        );

        $this->traderRepository->attachOrganization($trader->id, $organization->id);

        return $this->traderRepository->getById($trader->id);
    }
}
