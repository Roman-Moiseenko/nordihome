<?php

namespace App\Listeners;

use App\Events\PricingHasCompleted;
use App\Modules\User\Service\SubscriptionService;
use App\Modules\User\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationPricingCompleted
{
    private array $users;

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
    public function handle(PricingHasCompleted $event): void
    {
        //TODO Уведомляем менеджеров, что цены изменились

        //TODO Уведомляем клиентов
        if (!empty($this->users)) {
            //Проверяем изменение цены
            //Пишем всем, у кого товар в избранном
        }
    }
}
