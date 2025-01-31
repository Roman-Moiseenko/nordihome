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


        Route::resource('contact', 'ContactController')->except(['show']); //CRUD
        Route::post('/contact/{contact}/draft', 'ContactController@draft')->name('contact.draft');
        Route::post('/contact/{contact}/published', 'ContactController@published')->name('contact.published');
        Route::post('/contact/{contact}/up', 'ContactController@up')->name('contact.up');
        Route::post('/contact/{contact}/down', 'ContactController@down')->name('contact.down');


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
