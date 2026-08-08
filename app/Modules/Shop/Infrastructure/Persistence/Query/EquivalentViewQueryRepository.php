<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use Illuminate\Support\Facades\DB;

class EquivalentViewQueryRepository
{

    public function getProductIds(int $id): array
    {
        return DB::table('equivalents_products as ep1')
            ->join('equivalents_products as ep2', 'ep2.equivalent_id', '=', 'ep1.equivalent_id')
            ->where('ep1.product_id', $id)
            ->where('ep2.product_id', '!=', $id)
            ->pluck('ep2.product_id')
            ->toArray();
    }
}
