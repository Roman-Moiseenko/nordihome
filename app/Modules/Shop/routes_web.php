<?php

use App\Modules\Shop\Controllers\CartController;
use App\Modules\Shop\Controllers\CatalogController;
use App\Modules\Shop\Controllers\ECommerceController;
use App\Modules\Shop\Controllers\FeedXMLController;
use App\Modules\Shop\Controllers\GroupController;
use App\Modules\Shop\Controllers\OrderController;
use App\Modules\Shop\Controllers\PageController;
use App\Modules\Shop\Controllers\ParserController;
use App\Modules\Shop\Controllers\PostController;
use App\Modules\Shop\Controllers\ProductController;
use App\Modules\Shop\Controllers\PromotionController;
use App\Modules\Shop\Controllers\SitemapXmlController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapXmlController::class, 'index'])->name('sitemap');

Route::get('/feed/{feed}/feed-google.xml', [FeedXMLController::class, 'google'])->name('google');
Route::get('/feed/{feed}/feed-yandex.yml', [FeedXMLController::class, 'yandex'])->name('yandex');
Route::post('/e-commerce/', [ECommerceController::class, 'e_commerce'])->name('e-commerce');



Route::group(
    [
        'as' => 'shop.',
        'middleware' => ['user_cookie_id'],
    ],
    function () {
        Route::get('/test', function (){
            return phpinfo();
        });

        Route::get('/', [PageController::class, 'home'])->name('home');

        Route::post('/', function () {return abort(404);});

        Route::post('/csrf-token', function () {
            return csrf_token();
        });
        Route::get('/shop/{old_slug}', [ProductController::class, 'old_slug']);

        Route::get('/page/news', [PageController::class, 'news'])->name('page.news');
        Route::get('/page/{slug}', [PageController::class, 'view'])->name('page.view');
        Route::post('/page/map', [PageController::class, 'map_data'])->name('page.map');

        Route::get('/posts/{slug}', [PostController::class, 'posts'])->name('posts.view');
        Route::get('/post/{slug}', [PostController::class, 'post'])->name('post.view');

        //Route::get('/news', [\App\Modules\Page\Controllers\NewsController::class, ''])

        Route::group([
            'as' => 'product.',
            'prefix' => 'product',
        ], function () {

            Route::post('/search', [ProductController::class, 'search'])->name('search');
            Route::post('/count-for-sell/{product}', [ProductController::class, 'count_for_sell'])->name('count-for-sell');
            Route::get('/{slug}', [ProductController::class, 'view'])->name('view');
            Route::get('/draft/{product}', [ProductController::class, 'view_draft'])->name('view-draft');

        //    Route::get('/review/{review}', [ProductController::class, 'review'])->name('review.show');
        });

        Route::group([
            'as' => 'category.',
            'prefix' => 'catalog',
        ], function () {
            Route::post('/search', [CatalogController::class, 'search'])->name('search');
            Route::get('/{slug}', [CatalogController::class, 'view'])->name('view');
            Route::get('/', [CatalogController::class, 'index'])->name('index');
        });

        Route::get('/cart', [CartController::class, 'view'])->name('cart.view');


        Route::get('/promotion/{slug}', [PromotionController::class, 'view'])->name('promotion.view');
        Route::get('/group/{slug}', [GroupController::class, 'view'])->name('group.view');
        //Корзина AJAX
        Route::group([
            'as' => 'cart.',
            'prefix' => 'cart_post',
        ], function () {
            Route::post('/cart', [CartController::class, 'cart'])->name('all');
            Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
       //     Route::post('/sub/{product}', [CartController::class, 'sub'])->name('sub');
       //     Route::post('/set/{product}', [CartController::class, 'set'])->name('set');
      //      Route::post('/check/{product}', [CartController::class, 'check'])->name('check');
        //    Route::post('/check-all', [CartController::class, 'check_all'])->name('check-all');
            Route::post('/remove/{product}', [CartController::class, 'remove'])->name('remove');
            Route::post('/clear', [CartController::class, 'clear'])->name('clear');
        });
        Route::group([
            'as' => 'order.',
            'prefix' => 'order',
        ], function () {
            Route::post('/create', [OrderController::class, 'create'])->name('create');
            Route::put('/create', [OrderController::class, 'store']);
            Route::post('/create-parser', [OrderController::class, 'create_parser'])->name('create-parser');
            Route::post('/create-cart', [OrderController::class, 'create_cart'])->name('create-cart');
            Route::get('/create-click', function () {
                abort(404);
            });

            Route::post('/create-click', [OrderController::class, 'create_click'])->name('create-click');
            Route::put('/create-parser', [OrderController::class, 'store_parser']);

            //ajax
            //Route::post('/payment', [OrderController::class, 'payment'])->name('payment');
            Route::post('/checkorder', [OrderController::class, 'checkorder'])->name('checkorder');
            Route::post('/coupon', [OrderController::class, 'coupon'])->name('coupon');
        });

        Route::group([
            'as' => 'parser.'
        ],
            function () {
                Route::get('/calculate', [ParserController::class, 'view'])->name('view');
                Route::post('/parser/search', [ParserController::class, 'search'])->name('search');
                Route::post('/parser/clear', [ParserController::class, 'clear'])->name('clear');
                Route::post('/parser/{product}/remove', [ParserController::class, 'remove'])->name('remove');
                Route::post('/parser/{product}/add', [ParserController::class, 'add'])->name('add');
                Route::post('/parser/{product}/sub', [ParserController::class, 'sub'])->name('sub');
                Route::post('/parser/{product}/set', [ParserController::class, 'set'])->name('set');
            }
        );
    }

);
