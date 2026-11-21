<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Trader;

use App\Modules\Accounting\Application\DTOs\Trader\TraderViewData;
use App\Modules\Accounting\Domain\Interfaces\TraderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewTraderQuery
{
    public function __construct(
        private TraderRepositoryInterface $traderRepository,
    ) {
    }

    public function execute(int $id, UserPermission $permission): TraderViewData
    {
        if (!$permission->can('accounting.trader.view')) {
            throw new AccessDeniedException();
        }

        return TraderViewData::fromEntity($this->traderRepository->getById($id));
    }
}
