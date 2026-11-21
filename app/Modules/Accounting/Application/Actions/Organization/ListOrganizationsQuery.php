<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Accounting\Infrastructure\Models\Organization;

readonly class ListOrganizationsQuery
{
    /**
     * @return array<int, array{id: int, short_name: string, inn: string, full_name: string, account: string}>
     */
    public function execute(): array
    {
        return Organization::active()
            ->orderBy('short_name')
            ->get()
            ->map(fn(Organization $organization) => [
                'id' => $organization->id,
                'short_name' => $organization->short_name,
                'inn' => $organization->inn,
                'full_name' => $organization->full_name,
                'account' => $organization->pay_account,
            ])
            ->toArray();
    }
}
