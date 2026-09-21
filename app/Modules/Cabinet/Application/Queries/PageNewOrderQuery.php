<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Auth\Domain\Interfaces\ClientRepositoryInterface;
use App\Modules\Cabinet\Application\DTOs\Pages\NewOrderData;
use App\Modules\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

readonly class PageNewOrderQuery
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ClientRepositoryInterface $clientRepository,
    )
    {

    }

    public function execute(int $orderId, ClientContext $context): NewOrderData
    {
        $client = $this->clientRepository->findById($context->id);
        $title = $client->fullName->getValue();

        $orderEntity = $this->orderRepository->getById($orderId);

        $eArray = [];
        foreach ($orderEntity->items as $item) {
            $eArray[] = [
                'id' => $item->productId,
                'quantity' => $item->quantity,
            ];
        }
        $meta = new SeoData('Заказ сформирован | ' . $title, '');
        return new NewOrderData(
            meta: $meta,
            numberOrder: $orderEntity->number,
            dateOrder: $orderEntity->createdAt->format('d.m.Y'),
            eArray: $eArray,
        );
    }
}
