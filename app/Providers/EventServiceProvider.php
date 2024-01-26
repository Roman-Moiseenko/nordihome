<?php

namespace App\Providers;

use App\Events\ProductHasParsed;
use App\Events\PromotionHasMoved;
use App\Events\UserHasRegistered;
use App\Listeners\ParserNotification;
use App\Listeners\ParsingImageProduct;
use App\Listeners\PromotionNotification;
use App\Listeners\WelcomToShop;
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
            PromotionNotification::class
        ],
        ProductHasParsed::class => [
            ParsingImageProduct::class,
            ParserNotification::class
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
