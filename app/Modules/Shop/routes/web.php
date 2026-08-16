<?php

use App\Modules\Shop\Controllers\ECommerceController;
use App\Modules\Shop\Controllers\FeedXMLController;
use App\Modules\Shop\Controllers\GroupController;
use App\Modules\Shop\Controllers\PageController;
use App\Modules\Shop\Controllers\PromotionController;
use App\Modules\Shop\Controllers\SitemapXmlController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\CatalogController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\CheckoutController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\IkeaController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\PostController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ProductController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\RoomController;
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
        // Route::get('/shop/{old_slug}', [ProductController::class, 'old_slug']);

        Route::get('/page/news', [PageController::class, 'news'])->name('page.news');
        Route::get('/page/{slug}', [PageController::class, 'view'])->name('page.view');
        Route::post('/page/map', [PageController::class, 'map_data'])->name('page.map');

        Route::get('/posts/{slug}', [PostController::class, 'posts'])->name('posts.view');
        Route::get('/post/{slug}', [PostController::class, 'post'])->name('post.view');

        //Route::get('/news', [\App\Modules\Page\Controllers\NewsController::class, ''])

        Route::group([
            'as' => 'product.',
            'prefix' => 'shop',
        ], function () {


            Route::post('/search', [ProductController::class, 'search'])->name('search');
            Route::get('/search', [ProductController::class, 'searchIndex'])->name('search-index');
            Route::post('/count-for-sell/{product}', [ProductController::class, 'count_for_sell'])->name('count-for-sell');
            Route::get('/{slug}', [ProductController::class, 'view'])->name('view');
            Route::get('/draft/{product}', [ProductController::class, 'view_draft'])->name('view-draft');

            //    Route::get('/review/{review}', [ProductController::class, 'review'])->name('review.show');
        });

        Route::group([
            'as' => 'category.',
            'prefix' => 'catalog',
        ], function () {
            Route::get('/{slug}', [CatalogController::class, 'view'])->name('view');
            Route::get('/', [CatalogController::class, 'index'])->name('index');
        });

        Route::group([
            'as' => 'ikea.',
            'prefix' => 'ikea',
        ], function () {
            Route::get('/', [IkeaController::class, 'index'])->name('index');
            Route::get('/category/{slug}', [IkeaController::class, 'view'])->name('view');
            Route::get('/product/{code}', [IkeaController::class, 'product'])->name('product');

        });

        Route::group([
            'as' => 'room.',
            'prefix' => 'room',
        ], function () {

            Route::get('/', [RoomController::class, 'index'])->name('index');
            Route::get('/{slug}', [RoomController::class, 'view'])->name('view');

        });


        Route::get('/novelty', [CatalogController::class, 'novelty'])->name('novelty');

        Route::get('/promotion/{slug}', [PromotionController::class, 'view'])->name('promotion.view');
        Route::get('/group/{slug}', [GroupController::class, 'view'])->name('group.view');

        //Корзина





        //CHECKOUT
        Route::group([
            'as' => 'order.',
            'prefix' => 'order',
        ], function () {

            //В один клик без авторизации
            Route::post('/create-click', [CheckoutController::class, 'create_click'])->name('create-click');

            Route::middleware(['auth', 'role:client'])->group(function () {
                Route::post('/create', [CheckoutController::class, 'create'])->name('create');
                Route::put('/create', [CheckoutController::class, 'store']);

                Route::post('/checkorder', [CheckoutController::class, 'checkorder'])->name('checkorder');
                Route::post('/coupon', [CheckoutController::class, 'coupon'])->name('coupon');
            });
            Route::get('/create-click', function () {
                abort(404);
            });

        });

    }

);
