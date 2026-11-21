<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Trader;

use App\Modules\Accounting\Application\DTOs\Trader\TraderOrganizationSetData;
use App\Modules\Accounting\Domain\Interfaces\TraderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class AttachOrganizationToTraderUseCase
{
    public function __construct(
        private TraderRepositoryInterface $traderRepository,
    ) {
    }

    public function execute(int $id, TraderOrganizationSetData $dto, UserPermission $permission): void
    {
        if (!$permission->can('accounting.trader.edit')) {
            throw new AccessDeniedException();
        }

        $this->traderRepository->attachOrganization($id, $dto->organization);
    }
}
