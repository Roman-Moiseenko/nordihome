<?php
declare(strict_types=1);

namespace App\Modules\Order\Listeners;


use App\Modules\Mail\Job\SendSystemMail;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Events\ExpenseHasCompleted;

class UserWriteReview
{

    public function handle(ExpenseHasCompleted $event): void
    {
        //TODO

//
       /* SendSystemMail::dispatch(
            $event->expense->order->user,
            new WriteReview($event->expense),
            Order::class,
            $event->expense->order->id
        );*/
    }
}
