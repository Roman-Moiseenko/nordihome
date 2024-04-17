<?php

namespace App\Listeners;

use App\Events\ProductHasPublished;
use App\Modules\User\Service\SubscriptionService;
use App\Modules\User\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationProductPublished
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
    public function handle(ProductHasPublished $event): void
    {
        //TODO Собираем группу новых товаров


    }
}
