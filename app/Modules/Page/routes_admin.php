<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'page',
        'as' => 'page.',
       // 'namespace' => 'Page',
    ],
    function () {
        Route::group([
            'prefix' => 'cache',
            'as' => 'cache.'
        ], function () {
            Route::post('/remove', 'CacheController@remove')->name('remove');
            Route::post('/create', 'CacheController@create')->name('create');

            Route::post('/clear', 'CacheController@clear')->name('clear');
            Route::get('/', 'CacheController@index')->name('index');
        });

        Route::group([
            'prefix' => 'widget',
            'as' => 'widget.'
        ], function () {
            Route::post('/set-widget/{widget}', 'WidgetController@set_widget')->name('set-widget');
            Route::post('/add-item/{widget}', 'WidgetController@add_item')->name('add-item');
            Route::post('/set-item/{item}', 'WidgetController@set_item')->name('set-item');
            Route::delete('/del-item/{item}', 'WidgetController@del_item')->name('del-item');
            Route::post('/toggle/{widget}', 'WidgetController@toggle')->name('toggle');
            Route::post('/up-item/{item}', 'WidgetController@up_item')->name('up-item');
            Route::post('/down-item/{item}', 'WidgetController@down_item')->name('down-item');
        });
        Route::resource('widget', 'WidgetController')->except(['create', 'edit', 'update']); //CRUD


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



        Route::group([
            'prefix' => 'banner',
            'as' => 'banner.'
        ], function () {
            Route::post('/set-banner/{banner}', 'BannerController@set_banner')->name('set-banner');
            Route::post('/add-item/{banner}', 'BannerController@add_item')->name('add-item');
            Route::post('/set-item/{item}', 'BannerController@set_item')->name('set-item');
            Route::delete('/del-item/{item}', 'BannerController@del_item')->name('del-item');
            Route::post('/toggle/{banner}', 'BannerController@toggle')->name('toggle');
            Route::post('/up-item/{item}', 'BannerController@up_item')->name('up-item');
            Route::post('/down-item/{item}', 'BannerController@down_item')->name('down-item');
        });

        Route::resource('banner', 'BannerController')->except(['create', 'edit', 'update']); //CRUD
    }
);
