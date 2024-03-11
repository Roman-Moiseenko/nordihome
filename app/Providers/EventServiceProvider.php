<?php

namespace App\Providers;

use App\Events\ArrivalHasCompleted;
use App\Events\MovementHasCompleted;
use App\Events\MovementHasCreated;
use App\Events\OrderHasCanceled;
use App\Events\OrderHasCreated;
use App\Events\PaymentHasPaid;
use App\Events\PointHasEstablished;
use App\Events\ProductHasParsed;
use App\Events\PromotionHasMoved;
use App\Events\ThrowableHasAppeared;
use App\Events\UserHasRegistered;
use App\Listeners\NotificationCanceledOrder;
use App\Listeners\NotificationNewArrival;
use App\Listeners\NotificationNewMovement;
use App\Listeners\NotificationNewOrder;
use App\Listeners\NotificationNewPointStorage;
use App\Listeners\NotificationNewProductParser;
use App\Listeners\NotificationThrowable;
use App\Listeners\ParsingImageProduct;
use App\Listeners\NotificationMovedPromotion;
use App\Listeners\WelcomToShop;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Service\PaymentService;
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
            PaymentService::class,
        ],
        ArrivalHasCompleted::class => [
            NotificationNewArrival::class,
        ],
        ThrowableHasAppeared::class => [
            NotificationThrowable::class
        ],
        OrderHasCanceled::class => [
            NotificationCanceledOrder::class,
        ],
        MovementHasCompleted::class => [
            //TODO Слушатель для события
        ],
        MovementHasCreated::class => [
            NotificationNewMovement::class
        ],
        PointHasEstablished::class => [
            NotificationNewPointStorage::class
        ],
        PaymentHasPaid::class => [

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
