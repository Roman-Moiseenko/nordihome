<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use Illuminate\Support\Facades\DB;

class ProductSearchQueryRepository
{


    public function getProductIdsBySearch(string $search): array
    {

        //MAINDO Умный поиск
        return DB::table('products')->where('name', 'LIKE', "%$search%")
            ->pluck('products.id')->toArray();
    }
}
