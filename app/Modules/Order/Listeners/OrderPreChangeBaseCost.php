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
        dd($orders);
        //TODO Заказы, которые в работе у менеджера или новые



//
        /* SendSystemMail::dispatch(
             $event->expense->order->user,
             new WriteReview($event->expense),
             Order::class,
             $event->expense->order->id
         );*/
    }
}
