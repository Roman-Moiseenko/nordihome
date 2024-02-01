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


//TODO Настроить ЧПУ
//Shop - функции магазина
Route::group(
    [
        'as' => 'shop.',
        'namespace' => 'App\Http\Controllers\Shop',
        'middleware' => ['user_cookie_id'],
    ],
    function () {

        //Route::post('/review', 'ReviewController@index')->name('review');

        Route::get('/page/{slug}', 'PageController@view')->name('page.view');
        Route::post('/page/map', 'PageController@map_data')->name('page.map');
        Route::put('/page/email', 'PageController@email')->name('page.email');

        Route::post('/product/search', 'ProductController@search')->name('product.search');
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
            Route::put('/create-parser', 'OrderController@store_parser');

            Route::get('/index', 'OrderController@index')->name('index');
            Route::get('/{order}/view', 'OrderController@view')->name('view');

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

        Route::group([
            'as' => 'wish.',
            'prefix' => 'wish'
        ], function () {
            Route::get('/', 'WishController@index')->name('index');
            Route::post('/toggle', 'WishController@toggle')->name('toggle');
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

        Route::group(
            [
                'prefix' => 'discount',
                'as' => 'discount.',
                'namespace' => 'Discount',
            ],
            function () {
                Route::post('/promotion/{promotion}/add-group', 'PromotionController@add_group')->name('promotion.add-group');
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

                //TODO Объеденить как Delivery????
                Route::get('/order', 'OrderController@index')->name('order.index');
                Route::get('/order/{order}', 'OrderController@show')->name('order.show');

                Route::get('/preorder', 'PreOrderController@index')->name('preorder.index');
                Route::get('/preorder/{order}', 'PreOrderController@show')->name('preorder.show');
                Route::get('/preorder/{order}/destroy', 'PreOrderController@destroy')->name('preorder.destroy');

                Route::get('/executed', 'ExecutedController@index')->name('executed.index');
                Route::get('/executed/{order}', 'ExecutedController@show')->name('executed.show');
                Route::get('/order/{order}/destroy', 'OrderController@destroy')->name('order.destroy');

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

        //AJAX Product-Image
        Route::post('product/{product}/file-upload', 'Product\ProductController@file_upload')->name('product.file-upload');
        Route::post('product/{product}/get-images', 'Product\ProductController@get_images')->name('product.get-images');
        Route::post('product/{product}/del-image', 'Product\ProductController@del_image')->name('product.del-image');
        Route::post('product/{product}/up-image', 'Product\ProductController@up_image')->name('product.up-image');
        Route::post('product/{product}/down-image', 'Product\ProductController@down_image')->name('product.down-image');
        Route::post('product/{product}/alt-image', 'Product\ProductController@alt_image')->name('product.alt-image');
        Route::post('product/search', 'Product\ProductController@search')->name('product.search');
        Route::post('product/search_bonus', 'Product\ProductController@search_bonus')->name('product.search-bonus');
        //
        Route::post('product/{product}/attr-modification', 'Product\ProductController@attr_modification')->name('product.attr-modification');

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

