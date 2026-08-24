<?php

namespace App\Modules\Accounting\Application\Actions\Trader;

use App\Modules\Accounting\Entity\Trader;

class GetDefaultTraderIdUseCase
{

    public function execute(): int
    {
        return Trader::default()->organization->id;
    }
}
