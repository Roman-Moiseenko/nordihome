<?php

namespace App\Providers;

use App\Events\ArrivalHasCompleted;
use App\Events\CouponHasCreated;
use App\Events\DepartureHasCompleted;
use App\Events\ExpenseHasDelivery;
use App\Events\MovementHasCompleted;
use App\Events\MovementHasCreated;
use App\Events\OrderHasCanceled;
use App\Events\OrderHasCompleted;
use App\Events\OrderHasCreated;
use App\Events\OrderHasLogger;
use App\Events\OrderHasPaid;
use App\Events\OrderHasPrepaid;
use App\Events\OrderHasRefund;
use App\Events\ParserPriceHasChange;
use App\Events\PaymentHasPaid;
use App\Events\PriceHasMinimum;
use App\Events\PricingHasCompleted;
use App\Events\ProductHasBlocked;
use App\Events\ProductHasFastCreate;
use App\Events\ProductHasParsed;
use App\Events\ProductHasPublished;
use App\Events\PromotionHasMoved;
use App\Events\ReserveHasTimeOut;
use App\Events\ReviewHasEdit;
use App\Events\SupplyHasCompleted;
use App\Events\SupplyHasSent;
use App\Events\ThrowableHasAppeared;
use App\Events\UserHasCreated;
use App\Events\UserHasRegistered;
use App\Listeners\CheckNotificationStatus;
use App\Listeners\NotificationArrivalCompleted;
use App\Listeners\NotificationCouponCreated;
use App\Listeners\NotificationDepartureNew;
use App\Listeners\NotificationExpenseAssembly;
use App\Listeners\NotificationExpenseDelivery;
use App\Listeners\NotificationMovementCompleted;
use App\Listeners\NotificationMovementNew;
use App\Listeners\NotificationNewLogger;
use App\Listeners\NotificationOrderCanceled;
use App\Listeners\NotificationOrderCompleted;
use App\Listeners\NotificationOrderNew;
use App\Listeners\NotificationOrderPaid;
use App\Listeners\NotificationOrderPrepaid;
use App\Listeners\NotificationParserPriceChange;
use App\Listeners\NotificationPaymentNew;
use App\Listeners\NotificationPriceMinimum;
use App\Listeners\NotificationPricingCompleted;
use App\Listeners\NotificationProductBlocked;
use App\Listeners\NotificationProductFastCreat;
use App\Listeners\NotificationProductParserNew;
use App\Listeners\NotificationProductPublished;
use App\Listeners\NotificationPromotionMoved;
use App\Listeners\NotificationRefundNew;
use App\Listeners\NotificationReserveTimeOut;
use App\Listeners\NotificationReviewEdit;
use App\Listeners\NotificationSupplyCompleted;
use App\Listeners\NotificationSupplySent;
use App\Listeners\NotificationThrowable;
use App\Listeners\NotificationUserCreated;
use App\Listeners\ParsingImageProduct;
use App\Listeners\WelcomeToShop;
use App\Modules\Accounting\Events\CurrencyHasUpdateFixed;
use App\Modules\Admin\Listeners\NewTaskStaff;
use App\Modules\Admin\Listeners\NotificationStaff;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Notification\Events\TelegramHasReceived;
use App\Modules\Notification\Service\NotificationService;
use App\Modules\Order\Events\ExpenseHasCompleted;
use App\Modules\Order\Listeners\OrderPreChangeBaseCost;
use App\Modules\Order\Listeners\UserMailExpenseCompleted;
use App\Modules\Order\Listeners\UserWriteReview;
use App\Modules\Order\Service\ExpenseService;
use App\Modules\Order\Service\OrderService;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSending;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TelegramHasReceived::class => [
            ExpenseService::class, //Сбор заказа
            DeliveryService::class, //Сбор? и доставка Заказа
            NotificationService::class, //Подтверждение уведомления
            //CalendarService::class, //Подтверждение записи
            OrderService::class, //Взятие в работу заказ
            /**
             * Добавляем классы, которые обрабатывают подтверждения из Телеграм.
             */

        ],
        //Модуль Order
        ExpenseHasCompleted::class => [
            UserMailExpenseCompleted::class,
            UserWriteReview::class,
            NotificationStaff::class,
            NewTaskStaff::class,
        ],

        //Изменился курс валюты
        CurrencyHasUpdateFixed::class => [
            OrderPreChangeBaseCost::class,

        ],


        //TODO Остальные проработать

        /* Registered::class => [
             SendEmailVerificationNotification::class,
         ],*/
        UserHasRegistered::class => [
            WelcomeToShop::class
        ],
        PromotionHasMoved::class => [
            NotificationPromotionMoved::class
        ],
        ProductHasParsed::class => [
            NotificationProductParserNew::class,
            ParsingImageProduct::class,

        ],
        OrderHasCreated::class => [
            NotificationOrderNew::class,
            //TODO Возможно добавить создание лидов


            /* DeliveryService::class,
             PaymentService::class,*/
        ],
        ArrivalHasCompleted::class => [
            NotificationArrivalCompleted::class,
        ],
        ThrowableHasAppeared::class => [
            NotificationThrowable::class
        ],
        OrderHasCanceled::class => [
            NotificationOrderCanceled::class,
        ],
        MovementHasCompleted::class => [
            NotificationMovementCompleted::class
        ],
        MovementHasCreated::class => [
            NotificationMovementNew::class
        ],
        /*PointHasEstablished::class => [
            NotificationNewPointStorage::class
        ],*/
        PaymentHasPaid::class => [
            NotificationPaymentNew::class,
        ],
        OrderHasLogger::class => [
            NotificationNewLogger::class,
        ],
        OrderHasRefund::class => [
            NotificationRefundNew::class
        ],

        OrderHasPrepaid::class => [
            NotificationOrderPrepaid::class,
        ],
        OrderHasPaid::class => [
            NotificationOrderPaid::class
        ],
        DepartureHasCompleted::class => [
            NotificationDepartureNew::class,
        ],
        NotificationSending::class => [
            CheckNotificationStatus::class,
        ],
        //
        UserHasCreated::class => [
            NotificationUserCreated::class,
        ],
        SupplyHasSent::class => [
            NotificationSupplySent::class
        ],
        SupplyHasCompleted::class => [
            NotificationSupplyCompleted::class,
        ],
        ReserveHasTimeOut::class => [
            NotificationReserveTimeOut::class,
        ],
        PricingHasCompleted::class => [
            NotificationPricingCompleted::class,
        ],
        ProductHasPublished::class => [
            NotificationProductPublished::class,
            ],

        //Удалить
        OrderHasCompleted::class => [
            NotificationOrderCompleted::class
        ],


        ExpenseHasDelivery::class => [
            NotificationExpenseDelivery::class
        ],

        PriceHasMinimum::class => [
            NotificationPriceMinimum::class,
        ],
        CouponHasCreated::class => [
            NotificationCouponCreated::class
        ],
        ReviewHasEdit::class => [
            NotificationReviewEdit::class,
        ],

        ProductHasBlocked::class => [
            NotificationProductBlocked::class,
        ],
        ParserPriceHasChange::class => [
            NotificationParserPriceChange::class,
        ],
        ProductHasFastCreate::class => [
            NotificationProductFastCreat::class,
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
