<?php

namespace App\Listeners;

use App\Events\ArrivalHasCompleted;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\User\Entity\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotificationNewArrival
{
    private $users;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //TODO Получить список Пользователей, которые подписались на рассылку
        $this->users = User::where('status', User::STATUS_ACTIVE)->get();
    }

    /**
     * Handle the event.
     */
    public function handle(ArrivalHasCompleted $event): void
    {
        //Формируем список товаров
        $product_ids = array_map(
            function (ArrivalProduct $item) {return $item->product_id;},
            $event->arrival->arrivalProducts()->getModels()
        );


        foreach ($this->users as $user) {
            //TODO примерный код - продумать систему подписок и сделать шаблон письма
            /*
            if (!empty($products = $user->subscribe->arrival($product_ids)))
                Mail::to($user->email)->queue(new ProductArrival($products));*/

        }

        //TODO Кого еще уведомлять? Сотрудники через телеграм
    }
}
