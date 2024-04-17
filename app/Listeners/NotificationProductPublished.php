<?php

namespace App\Listeners;

use App\Events\ProductHasPublished;
use App\Mail\ProductNew;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Service\SubscriptionService;
use App\Modules\User\UserRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

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
        //Собираем группу новых товаров за неделю
        $products = Product::where('published', true)->where('published_at', '>', Carbon::now()->subDays(7)->toDateString())->getModels();
        if (!empty($products)) {
            foreach ($this->users as $user) {
                Mail::to($user->email)->queue(new ProductNew($products));
            }
        }

    }
}
