<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Trader;

use App\Modules\Accounting\Application\DTOs\Trader\TraderUpdateData;
use App\Modules\Accounting\Domain\Entities\TraderEntity;
use App\Modules\Accounting\Domain\Interfaces\TraderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateTraderUseCase
{
    public function __construct(
        private TraderRepositoryInterface $traderRepository,
    ) {
    }

    public function execute(int $id, TraderUpdateData $dto, UserPermission $permission): TraderEntity
    {
        if (!$permission->can('accounting.trader.edit')) {
            throw new AccessDeniedException();
        }

        $trader = $this->traderRepository->getById($id);

        if ($dto->name !== null) $trader->name = $dto->name;
        if ($dto->description !== null) $trader->description = $dto->description;

        if ($dto->default !== null) {
            if ($dto->default) {
                $trader->default = true;
            } elseif ($trader->isDefault()) {
                throw new \DomainException('Нельзя отменить по-умолчанию');
            }
        }

        $trader = $this->traderRepository->save($trader);

        // Назначение организации продавца по-умолчанию (переехало из метода default()).
        if ($dto->organization !== null) {
            $this->traderRepository->setDefaultOrganization($id, $dto->organization);
        }

        return $this->traderRepository->getById($id);
    }
}
