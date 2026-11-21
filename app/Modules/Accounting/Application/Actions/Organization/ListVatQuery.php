<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\Organization;

use App\Modules\Guide\Entity\VAT;

readonly class ListVatQuery
{
    /**
     * @return array<int, array{id: int, name: string}>
     */
    public function execute(): array
    {
        return VAT::orderBy('value')
            ->get()
            ->map(fn(VAT $vat) => ['id' => $vat->id, 'name' => $vat->name])
            ->toArray();
    }
}
