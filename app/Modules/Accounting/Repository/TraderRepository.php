<?php

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\Trader;
use Illuminate\Contracts\Support\Arrayable;

class TraderRepository
{
    public function getTraders(): Arrayable
    {
        $organization_ids = [];
        foreach (Trader::active()->getModels() as $trader) {
            $ids = $trader->organizations()->pluck('id')->toArray();
            $organization_ids = array_merge($organization_ids, $ids);
        }

        //$trader_ids = Trader::active()->pluck('id')->toArray();

        return Organization::whereIn('id', $organization_ids)->get()->map(function (Organization $organization) {
            return [
                'id' => $organization->trader->id,
                'name' => $organization->trader->name,
                'organization_id' => $organization->id,
                'full_name' => $organization->full_name,
                'short_name' => $organization->short_name,
                'account' => $organization->pay_account,
                'inn' => $organization->inn,

            ];
        });
    }

    public function TraderToArray(Trader $trader): array
    {
        return array_merge($trader->toArray(), [
            'organization' => is_null($trader->organization) ? null : $trader->organization()->get()->toArray(),
        ]);
    }

    public function TraderWithToArray(Trader $trader): array
    {
        return array_merge($this->TraderToArray($trader), [
            'organizations' => $trader->organizations,
        ]);
    }
}
