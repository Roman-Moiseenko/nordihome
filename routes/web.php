<?php

use App\Modules\Product\Entity\Modification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


//Shop

Route::get('/sitemap.xml', [App\Http\Controllers\Shop\SitemapXmlController::class, 'index'])->name('sitemap');

Route::group(
    [
        'middleware' => ['user_cookie_id'],
    ],
    function () {
        Route::get('/', [App\Http\Controllers\Shop\HomeController::class, 'index'])->name('home');
    }
);

//Shop - функции магазина
Route::group(
    [
        'as' => 'shop.',
        'namespace' => 'App\Http\Controllers\Shop',
        'middleware' => ['user_cookie_id'],
    ],
    function () {

        //Route::post('/review', 'ReviewController@index')->name('review');
        Route::get('/shop/{old_slug}', 'ProductController@old_slug');

        Route::get('/page/{slug}', 'PageController@view')->name('page.view');
        Route::post('/page/map', 'PageController@map_data')->name('page.map');
        Route::put('/page/email', 'PageController@email')->name('page.email');

        Route::post('/product/search', 'ProductController@search')->name('product.search');
        Route::post('/product/count-for-sell/{product}', 'ProductController@count_for_sell')->name('product.count-for-sell');
        Route::get('/product/{slug}', 'ProductController@view')->name('product.view');
        Route::post('/catalog/search', 'CatalogController@search')->name('category.search');
        Route::get('/catalog', 'CatalogController@index')->name('category.index');
        Route::get('/catalog/{slug}', 'CatalogController@view')->name('category.view');


        Route::get('/cart', 'CartController@view')->name('cart.view');

        Route::get('/promotion/{slug}', 'PromotionController@view')->name('promotion.view');
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



//Cabinet - функции кабинета клиента
Route::group([
    'as' => 'cabinet.',
    'prefix' => 'cabinet',
    'namespace' => 'App\Http\Controllers\User',
    'middleware' => ['user_cookie_id'],
],
    function() {
        Route::get('/', 'CabinetController@view')->name('view');
        Route::get('/profile', 'CabinetController@profile')->name('profile');
        Route::post('/fullname/{user}', 'CabinetController@fullname')->name('fullname');
        Route::post('/phone/{user}', 'CabinetController@phone')->name('phone');
        Route::post('/email/{user}', 'CabinetController@email')->name('email');
        Route::post('/password/{user}', 'CabinetController@password')->name('password');

        Route::group([
            'as' => 'options.',
            'prefix' => 'options',
        ], function() {
            Route::get('/', 'OptionsController@index')->name('index');

        });

        Route::group([
            'as' => 'wish.',
            'prefix' => 'wish'
        ], function () {
            Route::get('/', 'WishController@index')->name('index');
            Route::post('/clear', 'WishController@clear')->name('clear');
            Route::post('/get', 'WishController@get')->name('get');
            Route::post('/toggle/{product}', 'WishController@toggle')->name('toggle');
        });

        Route::group([
            'as' => 'order.',
            'prefix' => 'order'
        ], function () {
            Route::get('/', 'OrderController@index')->name('index');
            Route::get('/{order}', 'OrderController@view')->name('view');

        });
    }
);

Route::group(
    [
        'namespace' => 'App\Http\Controllers\User',
        'middleware' => ['user_cookie_id'],
    ],
    function () {
        //Route::get('/login', 'LoginController@showLoginForm')->name('login');
        Route::post('/login', 'LoginController@login')->name('login');
        Route::post('/login_register', 'LoginController@login_registration')->name('login_register');
        Route::any('/logout', 'LoginController@logout')->name('logout');

        Route::get('/register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('/register', 'RegisterController@register');

        Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('/password/reset', 'ResetPasswordController@reset')->name('password.update');
        Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
        Route::get('/password/confirm', 'ConfirmPasswordController@showConfirmForm')->name('password.confirm');
        Route::post('/password/confirm', 'ConfirmPasswordController@confirm');
        Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    }
);



Route::get('/verify/{token}', [\App\Http\Controllers\Auth\RegisterController::class, 'verify'])->name('register.verify');

//Admin
Route::get('/admin/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);//->name('admin.login');
Route::any('/admin/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('admin.logout');

Route::post('/file-upload', [\App\Http\Controllers\Admin\StaffController::class, 'test']);

Route::group(
    [
        'prefix' => 'admin',
        'as' => 'admin.',
        'namespace' => 'App\Http\Controllers\Admin',
        'middleware' => ['auth:admin', 'logger'],
    ],
    function () {
        Route::get('/', 'HomeController@index')->name('home');

        //Route::resource('users', 'UsersController');
        Route::get('/users', 'UsersController@index')->name('users.index');
        Route::get('/users/{user}', 'UsersController@show')->name('users.show');
        Route::post('/users/{user}/verify', 'UsersController@verify')->name('users.verify');

        Route::resource('staff', 'StaffController'); //CRUD

        Route::get('/staff/{staff}/security', 'StaffController@security')->name('staff.security');
        Route::post('/staff/password/{staff}', 'StaffController@password')->name('staff.password');
        Route::post('/staff/activate/{staff}', 'StaffController@activate')->name('staff.activate');
        Route::post('/staff/photo/{staff}', 'StaffController@setPhoto')->name('staff.photo');
        Route::post('/staff/response/{staff}', 'StaffController@response')->name('staff.response');

        //**** SHOP
        //Product
        Route::group(
            [
                'prefix' => 'product',
                'as' => 'product.',
                'namespace' => 'Product',
            ],
            function () {


                //Доп. - сменить категорию, добавить фото
                Route::get('/attribute/groups', 'AttributeController@groups')->name('attribute.groups');
                Route::delete('/attribute/group-destroy/{group}', 'AttributeController@group_destroy')->name('attribute.group-destroy');

                Route::post('/attribute/group-add', 'AttributeController@group_add')->name('attribute.group-add');
                Route::post('/attribute/group-rename/{group}', 'AttributeController@group_rename')->name('attribute.group-rename');
                Route::post('/attribute/variant-image/{variant}', 'AttributeController@variant_image')->name('attribute.variant-image');
                //Route::post('/attribute/get_by_categories', 'AttributeController@get_by_categories')->name('attribute.get-by-categories');

                Route::post('/attribute/{group}/group-up', 'AttributeController@group_up')->name('attribute.group-up');
                Route::post('/attribute/{group}/group-down', 'AttributeController@group_down')->name('attribute.group-down');

                Route::post('/category/{category}/up', 'CategoryController@up')->name('category.up');
                Route::post('/category/{category}/down', 'CategoryController@down')->name('category.down');
                Route::get('/category/{category}/child', 'CategoryController@child')->name('category.child');
                Route::post('/category/json_attributes', 'CategoryController@json_attributes')->name('attribute.json-attributes');

                Route::get('/tags', 'TagController@index')->name('tag.index');
                Route::post('/tag/create', 'TagController@create')->name('tag.create');
                Route::post('/tag/{tag}/rename', 'TagController@rename')->name('tag.rename');
                Route::delete('/tag/{tag}/destroy', 'TagController@destroy')->name('tag.destroy');

                Route::get('/equivalent', 'EquivalentController@index')->name('equivalent.index');
                Route::get('/equivalent/show', 'EquivalentController@show')->name('equivalent.show');
                Route::post('/equivalent/store', 'EquivalentController@create')->name('equivalent.store');
                Route::post('/equivalent/{equivalent}/rename', 'EquivalentController@rename')->name('equivalent.rename');
                Route::post('/equivalent/{equivalent}/add-product', 'EquivalentController@add_product')->name('equivalent.add-product');
                Route::delete('/equivalent/{equivalent}/del-product/{product}', 'EquivalentController@del_product')->name('equivalent.del-product');
                Route::delete('/equivalent/{equivalent}/destroy', 'EquivalentController@destroy')->name('equivalent.destroy');
                Route::post('/equivalent/{equivalent}/json-products', 'EquivalentController@json_products')->name('equivalent.json-products');

                Route::post('/group/{group}/add-product', 'GroupController@add_product')->name('group.add-product');
                Route::delete('/group/{group}/del-product', 'GroupController@del_product')->name('group.del-product');
                Route::post('/group/{group}/search', 'GroupController@search')->name('group.search');

                Route::post('/modification/{modification}/set-modifications', 'ModificationController@set_modifications')->name('modification.set-modifications');
                Route::post('/modification/search', 'ModificationController@search')->name('modification.search');
                Route::post('/modification/{modification}/add-product', 'ModificationController@add_product')->name('modification.add-product');
                Route::delete('/modification/{modification}/del-product', 'ModificationController@del_product')->name('modification.del-product');

                Route::get('/parser', 'ParserController@index')->name('parser.index');
                Route::get('/parser/{parser}/show', 'ParserController@show')->name('parser.show');
                Route::post('/parser/{parser}/block', 'ParserController@block')->name('parser.block');
                Route::post('/parser/{parser}/unblock', 'ParserController@unblock')->name('parser.unblock');

                Route::resource('brand', 'BrandController'); //CRUD
                Route::resource('category', 'CategoryController'); //CRUD
                Route::resource('attribute', 'AttributeController'); //CRUD
                Route::resource('equivalent', 'EquivalentController'); //CRUD
                Route::resource('group', 'GroupController'); //CRUD
                Route::resource('modification', 'ModificationController'); //CRUD
            }
        );
        //Discount
        Route::group(
            [
                'prefix' => 'discount',
                'as' => 'discount.',
                'namespace' => 'Discount',
            ],
            function () {
                Route::post('/promotion/{promotion}/add-group', 'PromotionController@add_group')->name('promotion.add-group');
                Route::post('/promotion/{promotion}/add-product', 'PromotionController@add_product')->name('promotion.add-product');
                Route::post('/promotion/{promotion}/search', 'PromotionController@search')->name('promotion.search');
                Route::post('/promotion/{promotion}/set-product/{product}', 'PromotionController@set_product')->name('promotion.set-product');
                Route::delete('/promotion/{promotion}/del-product/{product}', 'PromotionController@del_product')->name('promotion.del-product');



                Route::delete('/promotion/{promotion}/del-group/{group}', 'PromotionController@del_group')->name('promotion.del-group');
                Route::post('/promotion/{promotion}/published', 'PromotionController@published')->name('promotion.published');
                Route::post('/promotion/{promotion}/draft', 'PromotionController@draft')->name('promotion.draft');
                Route::post('/promotion/{promotion}/stop', 'PromotionController@stop')->name('promotion.stop');
                Route::post('/promotion/{promotion}/start', 'PromotionController@start')->name('promotion.start');

                Route::post('/discount/widget', 'DiscountController@widget')->name('discount.widget');
                Route::post('/discount/{discount}/published', 'DiscountController@published')->name('discount.published');
                Route::post('/discount/{discount}/draft', 'DiscountController@draft')->name('discount.draft');

                Route::resource('promotion', 'PromotionController'); //CRUD
                Route::resource('discount', 'DiscountController'); //CRUD
                //Route::resource('coupon', 'CouponController'); //CRUD
            }
        );

        //Delivery
        Route::group(
            [
                'prefix' => 'delivery',
                'as' => 'delivery.',
                'namespace' => 'Delivery',
            ],
            function () {
                //Просмотры - index
                Route::get('/', 'DeliveryController@index')->name('all');
                Route::get('/local', 'DeliveryController@index_local')->name('local');
                Route::get('/region', 'DeliveryController@index_region')->name('region');
                Route::get('/storage', 'DeliveryController@index_storage')->name('storage');
                //Действия
            }
        );
        //Sales - продажи

        Route::group(
            [
                'prefix' => 'sales',
                'as' => 'sales.',
                'namespace' => 'Sales',
            ],
            function () {
                Route::get('/cart', 'CartController@index')->name('cart.index');
                Route::get('/reserve', 'ReserveController@index')->name('reserve.index');
                Route::get('/wish', 'WishController@index')->name('wish.index');




           //     Route::get('/preorder', 'PreOrderController@index')->name('preorder.index');
             //   Route::get('/preorder/{order}', 'PreOrderController@show')->name('preorder.show');
                //Route::get('/preorder/{order}/destroy', 'PreOrderController@destroy')->name('preorder.destroy');

            //    Route::get('/parser', 'ParserController@index')->name('parser.index');
              //  Route::get('/parser/{order}', 'ParserController@show')->name('parser.show');
                //Route::get('/parser/{order}/destroy', 'ParserController@destroy')->name('parser.destroy');

             //   Route::get('/executed', 'ExecutedController@index')->name('executed.index');
               // Route::get('/executed/{order}', 'ExecutedController@show')->name('executed.show');

                Route::resource('order', 'OrderController');
                Route::group(
                    [
                        'prefix' => 'order',
                        'as' => 'order.',
                        //'namespace' => 'Sales',
                    ],
                    function() {
                        Route::post('/{order}/add-item', 'OrderController@add_item')->name('add-item');


                        Route::delete('/del-item/{item}', 'OrderController@del_item')->name('del-item');
                        Route::delete('/{order}/destroy', 'OrderController@destroy')->name('destroy');
                        Route::delete('/del-payment/{payment}', 'OrderController@del_payment')->name('del-payment');
                        Route::post('/{item}/update-quantity', 'OrderController@update_quantity')->name('update-quantity');
                        Route::post('/{item}/update-sell', 'OrderController@update_sell')->name('update-sell');

                        Route::post('/{item}/check-delivery', 'OrderController@check_delivery')->name('check-delivery');
                        Route::post('/{item}/check-assemblage', 'OrderController@check_assemblage')->name('check-assemblage');


                        Route::post('/{order}/set-manager', 'OrderController@set_manager')->name('set-manager');
                        Route::post('/{order}/set-logger', 'OrderController@set_logger')->name('set-logger');
                        Route::post('/{order}/set-reserve', 'OrderController@set_reserve')->name('set-reserve');

                        Route::post('/{order}/set-delivery', 'OrderController@set_delivery')->name('set-delivery');
                        Route::post('/{order}/set-moving', 'OrderController@set_moving')->name('set-moving');
                        Route::post('/{order}/set-payment', 'OrderController@set_payment')->name('set-payment');
                        Route::post('/{order}/paid-order', 'OrderController@paid_order')->name('paid-order');
                        Route::post('/{order}/canceled', 'OrderController@canceled')->name('canceled');
                        Route::post('/{order}/set-awaiting', 'OrderController@set_awaiting')->name('set-awaiting');
                        Route::post('/{order}/set-status', 'OrderController@set_status')->name('set-status');
                        Route::post('/{order}/completed', 'OrderController@completed')->name('completed');
                        Route::post('/{order}/refund', 'OrderController@refund')->name('refund');
                        Route::post('/paid-payment/{payment}', 'OrderController@paid_payment')->name('paid-payment');

                        Route::post('/search-user', 'OrderController@search_user')->name('search-user');
                        Route::post('/search', 'OrderController@search')->name('search');
                        Route::post('/get-to-order', 'OrderController@get_to_order')->name('get-to-order');

                    }
                );


            }
        );
        //Pages
        Route::group(
            [
                'prefix' => 'page',
                'as' => 'page.',
                'namespace' => 'Page',
            ],
            function () {
                Route::resource('widget', 'WidgetController'); //CRUD
                Route::post('/widget/ids', 'WidgetController@get_ids')->name('widget.ids');
                Route::post('/widget/{widget}/draft', 'WidgetController@draft')->name('widget.draft');
                Route::post('/widget/{widget}/activated', 'WidgetController@activated')->name('widget.activated');

                Route::resource('page', 'PageController'); //CRUD
                Route::post('/page/{page}/draft', 'PageController@draft')->name('page.draft');
                Route::post('/page/{page}/published', 'PageController@published')->name('page.published');
                Route::post('/page/{page}/text', 'PageController@text')->name('page.text');
            }
        );

        //ACCOUNTING
        Route::group(
            [
                'prefix' => 'accounting',
                'as' => 'accounting.',
                'namespace' => 'Accounting',
            ],
            function() {
                Route::resource('storage', 'StorageController')->except(['destroy']); //CRUD
                Route::resource('distributor', 'DistributorController'); //CRUD
                Route::resource('currency', 'CurrencyController'); //CRUD
                Route::resource('arrival', 'ArrivalController'); //CRUD
                Route::resource('movement', 'MovementController'); //CRUD
                Route::resource('departure', 'DepartureController'); //CRUD

                Route::post('/arrival/{arrival}/search', 'ArrivalController@search')->name('arrival.search');
                Route::post('/arrival/{arrival}/add', 'ArrivalController@add')->name('arrival.add');
                Route::post('/arrival/{arrival}/completed', 'ArrivalController@completed')->name('arrival.completed');
                Route::post('/arrival/{item}/set', 'ArrivalController@set')->name('arrival.set');
                Route::delete('/arrival/{item}/remove-item', 'ArrivalController@remove_item')->name('arrival.remove-item');

                Route::post('/movement/{movement}/search', 'MovementController@search')->name('movement.search');
                Route::post('/movement/{movement}/add', 'MovementController@add')->name('movement.add');
                Route::post('/movement/{movement}/completed', 'MovementController@completed')->name('movement.completed');
                Route::post('/movement/{item}/set', 'MovementController@set')->name('movement.set');
                Route::delete('/movement/{item}/remove-item', 'MovementController@remove_item')->name('movement.remove-item');

                Route::post('/departure/{departure}/search', 'DepartureController@search')->name('departure.search');
                Route::post('/departure/{departure}/add', 'DepartureController@add')->name('departure.add');
                Route::post('/departure/{departure}/completed', 'DepartureController@completed')->name('departure.completed');
                Route::post('/departure/{item}/set', 'DepartureController@set')->name('departure.set');
                Route::delete('/departure/{item}/remove-item', 'DepartureController@remove_item')->name('departure.remove-item');

            }
        );

        //ANALYTICS
        Route::group(
            [
                'prefix' => 'analytics',
                'as' => 'analytics.',
                'namespace' => 'Analytics',
            ],
            function() {
                Route::resource('cron', 'CronController')->only(['index', 'show']); //CRUD
                Route::resource('activity', 'ActivityController')->only(['index']); //CRUD

            }
        );

        //AJAX Product
        Route::post('product/{product}/file-upload', 'Product\ProductController@file_upload')->name('product.file-upload');
        Route::post('product/{product}/get-images', 'Product\ProductController@get_images')->name('product.get-images');
        Route::post('product/{product}/del-image', 'Product\ProductController@del_image')->name('product.del-image');
        Route::post('product/{product}/up-image', 'Product\ProductController@up_image')->name('product.up-image');
        Route::post('product/{product}/down-image', 'Product\ProductController@down_image')->name('product.down-image');
        Route::post('product/{product}/alt-image', 'Product\ProductController@alt_image')->name('product.alt-image');
        Route::post('product/search', 'Product\ProductController@search')->name('product.search');
        Route::post('product/search_bonus', 'Product\ProductController@search_bonus')->name('product.search-bonus');
        Route::post('product/{product}/attr-modification', 'Product\ProductController@attr_modification')->name('product.attr-modification');
        Route::post('product/toggle/{product}', 'Product\ProductController@toggle')->name('product.toggle');

        Route::resource('product', 'Product\ProductController'); //CRUD

        //Настройки
        Route::group(
            [
                'prefix' => 'settings',
                'as' => 'settings.',
                'namespace' => 'Settings',
            ],
            function () {
                Route::get('/shop', 'ShopSettingsController@index')->name('shop');
                Route::post('/shop', 'ShopSettingsController@update');
            }
        );
    }
);

//API

Route::any('/api/telegram', [\App\Http\Controllers\Api\TelegramController::class, 'get'])->name('api.telegram');
