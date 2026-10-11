<?php

namespace App\Listeners;

use App\Events\ProductHasPublished;


class NotificationProductPublished
{
    private array $users;

    public function __construct()
    {
   /*     if ($subscriptionService->check_subscription(self::class)) {
            $this->users = $userRepository->getUsersBySubscription(self::class);
        } else {
            $this->users = [];
        }
        */
    }

    /**
     * Handle the event.
     */
    public function handle(ProductHasPublished $event): void
    {
        //Собираем группу новых товаров за неделю
  /*      $products = Product::where('published', true)->where('published_at', '>', Carbon::now()->subDays(7)->toDateString())->getModels();
        if (!empty($products)) {
            foreach ($this->users as $user) {
                Mail::to($user->email)->queue(new ProductNew($products));
            }
        }
*/
    }
}
