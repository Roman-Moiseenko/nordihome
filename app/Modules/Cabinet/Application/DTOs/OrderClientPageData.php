<?php

namespace App\Modules\Cabinet\Application\DTOs;

use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

class OrderClientPageData
{
    public function __construct(
        public OrderClientData $order,
        public SeoData              $meta,

    )
    {
    }
}
