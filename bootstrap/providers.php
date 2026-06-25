<?php

return [
    App\Modules\Auth\Providers\AuthServiceProvider::class,
    App\Modules\Mailing\Providers\MailingServiceProvider::class,
    App\Modules\Shared\Providers\SharedServiceProvider::class,
    App\Modules\Testik\Providers\TestikServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\ViewServiceProvider::class,
];
