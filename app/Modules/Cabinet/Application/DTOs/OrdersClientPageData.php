<?php

namespace App\Modules\Cabinet\Application\DTOs;

use App\Modules\Shop\Application\DTOs\PageElements\PaginatorData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

class OrdersClientPageData
{
    public function __construct(
        /** @var OrderClientData[] $orders */
        public array $orders,
        public PaginatorData        $paginator,
        public SeoData              $meta,
    )
    {

    }
}
