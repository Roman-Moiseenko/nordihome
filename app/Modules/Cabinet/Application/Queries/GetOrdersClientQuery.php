<?php

namespace App\Modules\Cabinet\Application\Queries;

use App\Modules\Cabinet\Application\DTOs\OrderClientData;
use App\Modules\Cabinet\Application\DTOs\OrderInfoData;
use App\Modules\Cabinet\Application\DTOs\OrderInfoItemData;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Order\Infrastructure\Models\OrderItem;

class GetOrdersClientQuery
{
    public function __construct()
    {
    }

    /**
     * @param int $clientId
     * @return OrderClientData[]
     */
    public function execute(int $clientId): array
    {
        //MAINDO Возвращаем список заказов с пагинацией

        $ordersRaw = Order::orderBy('created_at')->where('client_id', $clientId)->paginate(15);
        $orders = [];
        /** @var Order $order */
        foreach ($ordersRaw as $order) {

            $items = [];

            /** @var OrderItem $item */
            foreach ($order->items as $item) {
                $items[] = new OrderInfoItemData(
                    productId: $item->product_id,
                    name: $item->product->name,
                    image: $item->product->getImage('mini'),
                    quantity: $item->quantity,
                    priceProduct: $item->product->getPrice(),
                    priceSell: $item->sell_cost,
                );
            }

            $info = new OrderInfoData(
                date: $order->created_at->translatedFormat('d F Y'),
                number: $order->number,
                totalAmount: $order->getTotalAmount(),
                status: $order->status->value,
                delivery: 0.0,
                address: '',
            );
            $orders[] = new OrderClientData(
                id: $order->id,
                info: $info,
                items: $items,
                additions: []
            );
        }

        return $orders;
    }
}
