<?php

return [
    App\Modules\Auth\Providers\AuthServiceProvider::class,
    App\Modules\Catalog\Providers\CatalogServiceProvider::class,
    App\Modules\Mailing\Providers\MailingServiceProvider::class,
    App\Modules\Parser\Providers\ParserServiceProvider::class,
    App\Modules\Shared\Providers\SharedServiceProvider::class,
    App\Modules\Shop\Providers\ShopServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    //App\Providers\ViewServiceProvider::class,
];
