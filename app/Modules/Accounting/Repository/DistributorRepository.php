<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\Distributor;

class DistributorRepository
{
    public function DistributorForAccounting(?Distributor $distributor):? array
    {
        if (is_null($distributor)) return null;
        return [
            'name' => $distributor->name,
            'short_name' => $distributor->organization->short_name,
            'full_name' => $distributor->organization->full_name,
            'inn' => $distributor->organization->inn,
            'debit' => $distributor->debit(),
            'credit' => $distributor->credit(),
            'currency' => $distributor->currency->sign,
            'foreign' => $distributor->foreign,
        ];
    }
}
