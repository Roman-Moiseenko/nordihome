<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Cabinet\Application\Actions\GetOrderClientData;
use App\Modules\Cabinet\Application\DTOs\OrderClientPageData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

readonly class GetOrderClientQuery
{
    public function __construct(
        private GetOrderClientData $getOrderClientData,

    )
    {
    }

    public function execute(int $orderId): OrderClientPageData
    {

        $order = $this->getOrderClientData->execute($orderId);
        return new OrderClientPageData(
            order: $order,
            meta: new SeoData("Заказ " . $order->info->number . ' от ' . $order->info->date , ''),
        );
    }
}
