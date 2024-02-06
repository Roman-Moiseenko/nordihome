<?php

namespace App\Providers;

use App\Events\ArrivalHasCompleted;
use App\Events\OrderHasCreated;
use App\Events\ProductHasParsed;
use App\Events\PromotionHasMoved;
use App\Events\UserHasRegistered;
use App\Listeners\NotificationNewArrival;
use App\Listeners\NotificationNewOrder;
use App\Listeners\NotificationNewProductParser;
use App\Listeners\ParsingImageProduct;
use App\Listeners\NotificationMovedPromotion;
use App\Listeners\WelcomToShop;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Order\Entity\Order\Order;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
       /* Registered::class => [
            SendEmailVerificationNotification::class,
        ],*/
        UserHasRegistered::class => [
            WelcomToShop::class
        ],
        PromotionHasMoved::class => [
            NotificationMovedPromotion::class
        ],
        ProductHasParsed::class => [
            ParsingImageProduct::class,
            NotificationNewProductParser::class
        ],
        OrderHasCreated::class => [
            NotificationNewOrder::class,
            DeliveryService::class,
        ],
        ArrivalHasCompleted::class => [
            NotificationNewArrival::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
