<?php

namespace App\Modules\Accounting\Application\Actions\Trader;

use App\Modules\Accounting\Domain\Interfaces\TraderRepositoryInterface;

readonly class GetDefaultTraderIdUseCase
{
    public function __construct(
        private TraderRepositoryInterface $traderRepository,
    ) {
    }

    public function execute(): int
    {
        return (int) $this->traderRepository->getDefaultTraderId();
    }
}
