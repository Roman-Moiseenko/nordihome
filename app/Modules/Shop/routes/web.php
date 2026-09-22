<?php


use App\Modules\Output\Presentation\Http\Controllers\Web\ECommerceAbstractController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\CatalogAbstractController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\CheckoutAbstractController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\GroupAbstractController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\IkeaController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\PageAbstractController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\PostController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ProductAbstractController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\PromotionAbstractController;
use App\Modules\Shop\Presentation\Http\Controllers\Web\RoomAbstractController;
use Illuminate\Support\Facades\Route;


Route::group(
    [
        'as' => 'shop.',
    ],
    function () {
        Route::get('/test', function (){
            return phpinfo();
        });

        Route::get('/', [PageAbstractController::class, 'home'])->name('home');
        Route::post('/', function () {return abort(404);});
        Route::post('/csrf-token', function () {
            return csrf_token();
        });
        // Route::get('/shop/{old_slug}', [ProductController::class, 'old_slug']);
        Route::get('/page/{slug}', [PageAbstractController::class, 'view'])->name('page.view');
        Route::get('/posts/{slug}', [PostController::class, 'posts'])->name('posts.view');
        Route::get('/post/{slug}', [PostController::class, 'post'])->name('post.view');

        Route::group([
            'as' => 'product.',
            'prefix' => 'shop',
        ], function () {
            Route::post('/search', [ProductAbstractController::class, 'search'])->name('search');
            Route::get('/search', [ProductAbstractController::class, 'searchIndex'])->name('search-index');
            Route::post('/count-for-sell/{product}', [ProductAbstractController::class, 'count_for_sell'])->name('count-for-sell');
            Route::get('/{slug}', [ProductAbstractController::class, 'view'])->name('view');
            Route::get('/draft/{product}', [ProductAbstractController::class, 'view_draft'])->name('view-draft');

            //    Route::get('/review/{review}', [ProductController::class, 'review'])->name('review.show');
        });

        Route::group([
            'as' => 'category.',
            'prefix' => 'catalog',
        ], function () {
            Route::get('/{slug}', [CatalogAbstractController::class, 'view'])->name('view');
            Route::get('/', [CatalogAbstractController::class, 'index'])->name('index');
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

            Route::get('/', [RoomAbstractController::class, 'index'])->name('index');
            Route::get('/{slug}', [RoomAbstractController::class, 'view'])->name('view');
        });
        Route::get('/novelty', [CatalogAbstractController::class, 'novelty'])->name('novelty');

        Route::get('/promotion/{slug}', [PromotionAbstractController::class, 'view'])->name('promotion.view');
        Route::get('/group/{slug}', [GroupAbstractController::class, 'view'])->name('group.view');

        //CHECKOUT
        Route::group([
            'as' => 'order.',
            'prefix' => 'order',
        ], function () {

            //В один клик без авторизации
            Route::post('/create-click', [CheckoutAbstractController::class, 'create_click'])->name('create-click');

            Route::middleware(['auth', 'role:client'])->group(function () {
                Route::post('/create', [CheckoutAbstractController::class, 'create'])->name('create');
                Route::put('/create', [CheckoutAbstractController::class, 'store']);
                //Route::post('/checkorder', [CheckoutController::class, 'checkorder'])->name('checkorder');
                Route::post('/coupon', [CheckoutAbstractController::class, 'coupon'])->name('coupon');
            });
            Route::get('/create-click', function () {
                abort(404);
            });

        });

    }

);
