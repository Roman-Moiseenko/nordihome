<?php

namespace App\Listeners;

use App\Events\ArrivalHasCompleted;
use App\Mail\ProductArrival;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\User\Entity\User;
use App\Modules\User\Service\SubscriptionService;
use App\Modules\User\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotificationArrivalCompleted
{
    private array $users;

    /**
     * Create the event listener.
     */
    public function __construct(SubscriptionService $subscriptionService, UserRepository $userRepository)
    {
        if ($subscriptionService->check_subscription(self::class)) {
            $this->users = $userRepository->getUsersBySubscription(self::class);
        } else {
            $this->users = [];
        }
    }

    /**
     * Handle the event.
     */
    public function handle(ArrivalHasCompleted $event): void
    {
        //TODO Уведомляем Сотрудников через телеграм ??


        if (!empty($this->users)) {
            $products = []; //Список товаров в поступлении, которых не было
            /** @var ArrivalProduct $arrivalProduct */
            foreach ($event->arrival->arrivalProducts()->getModels() as $arrivalProduct) {
                if ($arrivalProduct->product->getCountSell() <=  $arrivalProduct->quantity) { //Кол-во на продажу, до поступления было = 0
                    $products[] = $arrivalProduct->product;
                }
            }

            foreach ($this->users as $user) { //Проверяем всех пользователей, кто подписан на уведомление
                $user_products = []; //Список товаров из Избранное, которые есть в Поступлении
                foreach ($products as $product) {
                    if ($user->isWish($product->id)) $user_products[] = $product; //Если есть в избранном, добавляем на уведомление
                }
                if (!empty($user_products)) //Одним письмом клиенту, о поступлении товаров из избранного
                    Mail::to($user->email)->queue(new ProductArrival($user_products, $user));
            }
        }
    }
}
