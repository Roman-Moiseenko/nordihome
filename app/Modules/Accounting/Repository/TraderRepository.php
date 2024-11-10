<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\Trader;
use Illuminate\Contracts\Support\Arrayable;

class TraderRepository
{
    public function getTraders(): Arrayable
    {
        return Trader::active()->get()->map(function (Trader $item) {
            return [
                'id' => $item->id,
                'full_name' => $item->organization->full_name,
                'shot_name' => $item->organization->short_name,
                'account' => $item->organization->pay_account,
                'inn' => $item->organization->inn,
            ];
        });
    }
}
