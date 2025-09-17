<?php


use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', 'SitemapXmlController@index')->name('sitemap');

Route::group(
    [
        'as' => 'shop.',
        'middleware' => ['user_cookie_id'],
    ],
    function () {

        Route::get('/test', function (){
            return phpinfo();
        });

        Route::get('/', 'PageController@home')->name('home');

        Route::post('/', function () {return abort(404);});

        Route::post('/csrf-token', function () {
            return csrf_token();
        });
        Route::get('/shop/{old_slug}', 'ProductController@old_slug');

        Route::get('/page/{slug}', 'PageController@view')->name('page.view');
        Route::post('/page/map', 'PageController@map_data')->name('page.map');

        Route::group([
            'as' => 'product.',
            'prefix' => 'product',
        ], function () {

            Route::post('/search', 'ProductController@search')->name('search');
            Route::post('/count-for-sell/{product}', 'ProductController@count_for_sell')->name('count-for-sell');
            Route::get('/{slug}', 'ProductController@view')->name('view');
            Route::get('/draft/{product}', 'ProductController@view_draft')->name('view-draft');

            Route::get('/review/{review}', 'ProductController@review')->name('review.show');
        });

        Route::group([
            'as' => 'category.',
            'prefix' => 'catalog',
        ], function () {
            Route::post('/search', 'CatalogController@search')->name('search');
            Route::get('/{slug}', 'CatalogController@view')->name('view');
            Route::get('/', 'CatalogController@index')->name('index');
        });

        Route::get('/cart', 'CartController@view')->name('cart.view');


        Route::get('/promotion/{slug}', 'PromotionController@view')->name('promotion.view');
        Route::get('/group/{slug}', 'GroupController@view')->name('group.view');
        //Корзина AJAX
        Route::group([
            'as' => 'cart.',
            'prefix' => 'cart_post',
        ], function () {
            Route::post('/cart', 'CartController@cart')->name('all');
            Route::post('/add/{product}', 'CartController@add')->name('add');
            Route::post('/sub/{product}', 'CartController@sub')->name('sub');
            Route::post('/set/{product}', 'CartController@set')->name('set');
            Route::post('/check/{product}', 'CartController@check')->name('check');
            Route::post('/check-all', 'CartController@check_all')->name('check-all');
            Route::post('/remove/{product}', 'CartController@remove')->name('remove');
            Route::post('/clear', 'CartController@clear')->name('clear');
        });
        Route::group([
            'as' => 'order.',
            'prefix' => 'order',
        ], function () {
            Route::post('/create', 'OrderController@create')->name('create');
            Route::put('/create', 'OrderController@store');
            Route::post('/create-parser', 'OrderController@create_parser')->name('create-parser');
            Route::post('/create-cart', 'OrderController@create_cart')->name('create-cart');
            Route::get('/create-click', function () {
                abort(404);
            });

            Route::post('/create-click', 'OrderController@create_click')->name('create-click');





            Route::put('/create-parser', 'OrderController@store_parser');

            //ajax
            Route::post('/payment', 'OrderController@payment')->name('payment');
            Route::post('/checkorder', 'OrderController@checkorder')->name('checkorder');
            Route::post('/coupon', 'OrderController@coupon')->name('coupon');
        });

        Route::group([
            'as' => 'parser.'
        ],
            function () {
                Route::get('/calculate', 'ParserController@view')->name('view');
                Route::post('/parser/search', 'ParserController@search')->name('search');
                Route::post('/parser/clear', 'ParserController@clear')->name('clear');
                Route::post('/parser/{product}/remove', 'ParserController@remove')->name('remove');
                Route::post('/parser/{product}/add', 'ParserController@add')->name('add');
                Route::post('/parser/{product}/sub', 'ParserController@sub')->name('sub');
                Route::post('/parser/{product}/set', 'ParserController@set')->name('set');
            }
        );


    }
);
