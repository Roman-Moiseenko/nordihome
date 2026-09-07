<?php

namespace App\Modules\Accounting\Application\Actions\Trader;

use App\Modules\Accounting\Application\DTOs\OrganizationListData;
use App\Modules\Accounting\Entity\Organization;

class ListTradersUseCase
{
    /**
     * @return OrganizationListData[]
     */
    public function execute(): array
    {
        $traders = Organization::has('trader')->getModels();

        return array_map(fn(Organization $organization) => new OrganizationListData(
            id: $organization->id,
            shortName: $organization->short_name,
            INN: $organization->inn,
        ), $traders);

    }
}
