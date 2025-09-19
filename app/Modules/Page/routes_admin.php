<?php

use App\Modules\Page\Controllers\CacheController;
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
            Route::post('/create', 'CacheController@create')->name('create');
            Route::post('/categories', 'CacheController@categories')->name('categories');
            Route::post('/products', 'CacheController@products')->name('products');
            Route::post('/pages', 'CacheController@pages')->name('pages');

            Route::post('/clear', 'CacheController@clear')->name('clear');
            Route::get('/', 'CacheController@index')->name('index');
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
                Route::post('/set-widget/{product}', 'ProductWidgetController@set_widget')->name('set-widget');
                Route::post('/add-item/{product}', 'ProductWidgetController@add_item')->name('add-item');
                Route::post('/set-item/{item}', 'ProductWidgetController@set_item')->name('set-item');
                Route::delete('/del-item/{item}', 'ProductWidgetController@del_item')->name('del-item');
                Route::post('/toggle/{product}', 'ProductWidgetController@toggle')->name('toggle');
                Route::post('/up-item/{item}', 'ProductWidgetController@up_item')->name('up-item');
                Route::post('/down-item/{item}', 'ProductWidgetController@down_item')->name('down-item');

            });
            Route::resource('product', 'ProductWidgetController')->except(['create', 'edit', 'update']); //CRUD

            Route::group([
                'prefix' => 'banner',
                'as' => 'banner.'
            ], function () {
                Route::post('/set-banner/{banner}', 'BannerWidgetController@set_banner')->name('set-banner');
                Route::post('/add-item/{banner}', 'BannerWidgetController@add_item')->name('add-item');
                Route::post('/set-item/{item}', 'BannerWidgetController@set_item')->name('set-item');
                Route::delete('/del-item/{item}', 'BannerWidgetController@del_item')->name('del-item');
                Route::post('/toggle/{banner}', 'BannerWidgetController@toggle')->name('toggle');
                Route::post('/up-item/{item}', 'BannerWidgetController@up_item')->name('up-item');
                Route::post('/down-item/{item}', 'BannerWidgetController@down_item')->name('down-item');
            });
            Route::resource('banner', 'BannerWidgetController')->except(['create', 'edit', 'update']); //CRUD


            Route::group([
                'prefix' => 'promotion',
                'as' => 'promotion.'
            ], function () {
                Route::post('/set-widget/{promotion}', 'PromotionWidgetController@set_widget')->name('set-widget');
                Route::post('/toggle/{promotion}', 'PromotionWidgetController@toggle')->name('toggle');

            });
            Route::resource('promotion', 'PromotionWidgetController')->except(['create', 'edit', 'update']); //CRUD

            Route::group([
                'prefix' => 'text',
                'as' => 'text.'
            ], function () {
                Route::post('/set-widget/{text}', 'TextWidgetController@set_widget')->name('set-widget');
                Route::post('/add-item/{text}', 'TextWidgetController@add_item')->name('add-item');
                Route::post('/set-item/{item}', 'TextWidgetController@set_item')->name('set-item');
                Route::delete('/del-item/{item}', 'TextWidgetController@del_item')->name('del-item');
                Route::post('/toggle/{text}', 'TextWidgetController@toggle')->name('toggle');
                Route::post('/up-item/{item}', 'TextWidgetController@up_item')->name('up-item');
                Route::post('/down-item/{item}', 'TextWidgetController@down_item')->name('down-item');
            });
            Route::resource('text', 'TextWidgetController')->except(['create', 'edit', 'update']); //CRUD
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
