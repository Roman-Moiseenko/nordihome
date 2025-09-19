<?php

use App\Modules\Page\Controllers\BannerWidgetController;
use App\Modules\Page\Controllers\CacheController;
use App\Modules\Page\Controllers\ProductWidgetController;
use App\Modules\Page\Controllers\PromotionWidgetController;
use App\Modules\Page\Controllers\TextWidgetController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'page',
        'as' => 'page.',
    //    'namespace' => 'App\Modules\Page\Controllers',
    ],
    function () {
        Route::group([
            'prefix' => 'cache',
            'as' => 'cache.' //'CacheController@remove'
        ], function () {
            Route::post('/remove', [CacheController::class, 'clear'])->name('remove');
            Route::post('/create', [CacheController::class, 'create'])->name('create');
            Route::post('/categories', [CacheController::class, 'categories'])->name('categories');
            Route::post('/products', [CacheController::class, 'products'])->name('products');
            Route::post('/pages', [CacheController::class, 'pages'])->name('pages');

            Route::post('/clear', [CacheController::class, 'clear'])->name('clear');
            Route::get('/', [CacheController::class, 'index'])->name('index');
        });

        //Виджеты
        Route::group([
            'prefix' => 'widget',
            'as' => 'widget.'
        ], function () {
            Route::group([
                'prefix' => 'product',
                'as' => 'product.'
            ], function () {
                Route::post('/set-widget/{widget}', [ProductWidgetController::class, 'set_widget'])->name('set-widget');
                Route::post('/add-item/{widget}', [ProductWidgetController::class, 'add_item'])->name('add-item');
                Route::post('/set-item/{item}', [ProductWidgetController::class, 'set_item'])->name('set-item');
                Route::delete('/del-item/{item}', [ProductWidgetController::class, 'del_item'])->name('del-item');
                Route::post('/toggle/{widget}', [ProductWidgetController::class, 'toggle'])->name('toggle');
                Route::post('/up-item/{item}', [ProductWidgetController::class, 'up_item'])->name('up-item');
                Route::post('/down-item/{item}', [ProductWidgetController::class, 'down_item'])->name('down-item');

                Route::get('/{widget}', [ProductWidgetController::class, 'show'])->name('show');
                Route::post('/', [ProductWidgetController::class, 'store'])->name('store');
                Route::delete('/{widget}', [ProductWidgetController::class, 'destroy'])->name('destroy');
                Route::get('/', [ProductWidgetController::class, 'index'])->name('index');
            });
            //Route::resource('product', 'ProductWidgetController')->except(['create', 'edit', 'update']); //CRUD

            Route::group([
                'prefix' => 'banner',
                'as' => 'banner.'
            ], function () {
                Route::post('/set-banner/{widget}', [BannerWidgetController::class, 'set_widget'])->name('set-widget');
                Route::post('/add-item/{widget}', [BannerWidgetController::class, 'add_item'])->name('add-item');
                Route::post('/set-item/{item}', [BannerWidgetController::class, 'set_item'])->name('set-item');
                Route::delete('/del-item/{item}', [BannerWidgetController::class, 'del_item'])->name('del-item');
                Route::post('/toggle/{widget}', [BannerWidgetController::class, 'toggle'])->name('toggle');
                Route::post('/up-item/{item}', [BannerWidgetController::class, 'up_item'])->name('up-item');
                Route::post('/down-item/{item}', [BannerWidgetController::class, 'down_item'])->name('down-item');

                Route::get('/{widget}', [BannerWidgetController::class, 'show'])->name('show');
                Route::post('/', [BannerWidgetController::class, 'store'])->name('store');
                Route::delete('/{widget}', [BannerWidgetController::class, 'destroy'])->name('destroy');
                Route::get('/', [BannerWidgetController::class, 'index'])->name('index');
            });
           // Route::resource('banner', 'BannerWidgetController')->except(['create', 'edit', 'update']); //CRUD


            Route::group([
                'prefix' => 'promotion',
                'as' => 'promotion.'
            ], function () {
                Route::post('/set-widget/{widget}', [PromotionWidgetController::class, 'set_widget'])->name('set-widget');
                Route::post('/toggle/{widget}', [PromotionWidgetController::class, 'toggle'])->name('toggle');

                Route::get('/{widget}', [PromotionWidgetController::class, 'show'])->name('show');
                Route::post('/', [PromotionWidgetController::class, 'store'])->name('store');
                Route::delete('/{widget}', [PromotionWidgetController::class, 'destroy'])->name('destroy');
                Route::get('/', [PromotionWidgetController::class, 'index'])->name('index');
            });
          //  Route::resource('promotion', 'PromotionWidgetController')->except(['create', 'edit', 'update']); //CRUD

            Route::group([
                'prefix' => 'text',
                'as' => 'text.'
            ], function () {
                Route::post('/set-widget/{widget}', [TextWidgetController::class, 'set_widget'])->name('set-widget');
                Route::post('/add-item/{widget}', [TextWidgetController::class, 'add_item'])->name('add-item');
                Route::post('/set-item/{item}', [TextWidgetController::class, 'set_item'])->name('set-item');
                Route::delete('/del-item/{item}', [TextWidgetController::class, 'del_item'])->name('del-item');
                Route::post('/toggle/{widget}', [TextWidgetController::class, 'toggle'])->name('toggle');
                Route::post('/up-item/{item}', [TextWidgetController::class, 'up_item'])->name('up-item');
                Route::post('/down-item/{item}', [TextWidgetController::class, 'down_item'])->name('down-item');

                Route::get('/{widget}', [TextWidgetController::class, 'show'])->name('show');
                Route::post('/', [TextWidgetController::class, 'store'])->name('store');
                Route::delete('/{widget}', [TextWidgetController::class, 'destroy'])->name('destroy');
                Route::get('/', [TextWidgetController::class, 'index'])->name('index');
            });
           // Route::resource('text', 'TextWidgetController')->except(['create', 'edit', 'update']); //CRUD
        });



        Route::group([
            'prefix' => 'page',
            'as' => 'page.'
        ], function () {
            Route::post('/toggle/{page}', 'PageController@toggle')->name('toggle');
            Route::post('/set-info/{page}', 'PageController@set_info')->name('set-info');
            Route::post('/set-text/{page}', 'PageController@set_text')->name('set-text');
            Route::post('/up/{page}', 'PageController@up')->name('up');
            Route::post('/down/{page}', 'PageController@down')->name('down');
        });

        Route::resource('page', 'PageController')->except(['create', 'edit', 'update']); //CRUD

        Route::group([
            'prefix' => 'contact',
            'as' => 'contact.'
        ], function () {
            Route::post('/toggle/{contact}', 'ContactController@toggle')->name('toggle');
            Route::post('/up/{contact}', 'ContactController@up')->name('up');
            Route::post('/down/{contact}', 'ContactController@down')->name('down');
            Route::post('/set-info/{contact}', 'ContactController@set_info')->name('set-info');
        });
        Route::resource('contact', 'ContactController')->except(['show', 'create', 'edit', 'update']); //CRUD





    }
);
