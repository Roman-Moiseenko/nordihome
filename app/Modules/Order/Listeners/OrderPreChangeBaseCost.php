<?php

namespace App\Modules\Order\Listeners;

use App\Modules\Accounting\Events\CurrencyHasUpdateFixed;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Repository\OrderRepository;

class OrderPreChangeBaseCost
{

    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        //
        $this->repository = $repository;
    }

    public function handle(CurrencyHasUpdateFixed $event): void
    {

        $orders = $this->repository->getInWorkWithPreorder();

        //TODO Заказы, которые в работе у менеджера или новые
        /** @var Order $order */
        foreach ($orders as $order) {

            foreach ($order->items as $item) {
                if ($item->preorder) {
                    $item->base_cost = $item->product->getPrice(false, $order->user);
                    $item->sell_cost = $item->product->getPrice(false, $order->user);
                    $item->save();
                }
            }
        }


//
        /* SendSystemMail::dispatch(
             $event->expense->order->user,
             new WriteReview($event->expense),
             Order::class,
             $event->expense->order->id
         );*/
    }
}
