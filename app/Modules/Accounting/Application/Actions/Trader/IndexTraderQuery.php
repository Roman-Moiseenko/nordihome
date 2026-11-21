<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Trader;

use App\Modules\Accounting\Application\DTOs\Trader\TraderIndexData;
use App\Modules\Accounting\Domain\Entities\TraderEntity;
use App\Modules\Accounting\Domain\Interfaces\TraderRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class IndexTraderQuery
{
    public function __construct(
        private TraderRepositoryInterface $traderRepository,
    ) {
    }

    /**
     * @return TraderIndexData[]
     */
    public function execute(UserPermission $permission): array
    {
        if (!$permission->can('accounting.trader.view')) {
            throw new AccessDeniedException();
        }

        return array_map(
            fn(TraderEntity $trader) => TraderIndexData::fromEntity($trader),
            $this->traderRepository->getAll()
        );
    }
}
