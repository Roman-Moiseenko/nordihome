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
    'namespace' => 'App\Http\Controllers\Cabinet',
    'middleware' => ['user_cookie_id'],
],
    function () {
        Route::get('/', 'CabinetController@view')->name('view');
        Route::get('/profile', 'CabinetController@profile')->name('profile');
        Route::post('/fullname/{user}', 'CabinetController@fullname')->name('fullname');
        Route::post('/phone/{user}', 'CabinetController@phone')->name('phone');
        Route::post('/email/{user}', 'CabinetController@email')->name('email');
        Route::post('/password/{user}', 'CabinetController@password')->name('password');

        Route::group([
            'as' => 'options.',
            'prefix' => 'options',
        ], function () {
            Route::get('/', 'OptionsController@index')->name('index');
            Route::post('/subscription/{subscription}', 'OptionsController@subscription')->name('subscription');

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
        Route::group([
            'as' => 'review.',
            'prefix' => 'review',
        ], function() {
            Route::get('/', 'ReviewController@index')->name('index');
            Route::get('/show/{review}', 'ReviewController@show')->name('show');

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
        Route::get('/login', function () {
            abort(404);
        });
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

        Route::group(
            [
                'prefix' => 'user',
                'as' => 'user.',
                'namespace' => 'User',
            ],
            function () {
                Route::resource('subscription', 'SubscriptionController')->except(['create', 'store', 'destroy']); //CRUD
                //Subscription
                Route::group(
                    [
                        'prefix' => 'subscription',
                        'as' => 'subscription.',
                        //'namespace' => '',
                    ],
                    function () {
                        Route::post('/published/{subscription}', 'SubscriptionController@published')->name('published');
                        Route::post('/draft/{subscription}', 'SubscriptionController@draft')->name('draft');
                    }
                );
            }
        );
        Route::get('/staff/notification', 'StaffController@notification')->name('staff.notification');
        Route::post('/staff/notification-read/{notification}', 'StaffController@notification_read')->name('staff.notification-read');


        Route::post('/worker/{worker}/toggle', 'WorkerController@toggle')->name('worker.toggle');

        Route::get('/staff/{staff}/security', 'StaffController@security')->name('staff.security');
        Route::post('/staff/password/{staff}', 'StaffController@password')->name('staff.password');
        Route::post('/staff/activate/{staff}', 'StaffController@activate')->name('staff.activate');
        Route::post('/staff/photo/{staff}', 'StaffController@setPhoto')->name('staff.photo');
        Route::post('/staff/response/{staff}', 'StaffController@response')->name('staff.response');

        Route::resource('staff', 'StaffController'); //CRUD
        Route::resource('worker', 'WorkerController'); //CRUD
        //**** SHOP
        //Product
        Route::group(
            [
                'prefix' => 'product',
                'as' => 'product.',
                'namespace' => 'Product',
            ],
            function () {

                Route::post('/action', 'ProductController@action')->name('action');

                //Атрибуты
                Route::group([
                    'prefix' => 'attribute',
                    'as' => 'attribute.',
                ], function() {
                    //Доп. - сменить категорию, добавить фото
                    Route::get('/groups', 'AttributeController@groups')->name('groups');
                    Route::delete('/group-destroy/{group}', 'AttributeController@group_destroy')->name('group-destroy');

                    Route::post('/group-add', 'AttributeController@group_add')->name('group-add');
                    Route::post('/group-rename/{group}', 'AttributeController@group_rename')->name('group-rename');
                    Route::post('/variant-image/{variant}', 'AttributeController@variant_image')->name('variant-image');
                    //Route::post('/get_by_categories', 'AttributeController@get_by_categories')->name('get-by-categories');

                    Route::post('/group-up/{group}', 'AttributeController@group_up')->name('group-up');
                    Route::post('/group-down/{group}', 'AttributeController@group_down')->name('group-down');
                });


                Route::post('/category/{category}/up', 'CategoryController@up')->name('category.up');
                Route::post('/category/{category}/down', 'CategoryController@down')->name('category.down');
                Route::get('/category/{category}/child', 'CategoryController@child')->name('category.child');
                Route::post('/category/json_attributes', 'CategoryController@json_attributes')->name('attribute.json-attributes');

                Route::get('/tags', 'TagController@index')->name('tag.index');
                Route::post('/tag/create', 'TagController@create')->name('tag.create');
                Route::post('/tag/{tag}/rename', 'TagController@rename')->name('tag.rename');
                Route::delete('/tag/{tag}/destroy', 'TagController@destroy')->name('tag.destroy');

               // Route::get('/equivalent', 'EquivalentController@index')->name('equivalent.index');
                //Route::get('/equivalent/show', 'EquivalentController@show')->name('equivalent.show');
                //Route::post('/equivalent/store', 'EquivalentController@create')->name('equivalent.store');
                Route::post('/equivalent/{equivalent}/rename', 'EquivalentController@rename')->name('equivalent.rename');
                Route::post('/equivalent/{equivalent}/add-product', 'EquivalentController@add_product')->name('equivalent.add-product');
                Route::delete('/equivalent/{equivalent}/del-product/{product}', 'EquivalentController@del_product')->name('equivalent.del-product');
                //Route::delete('/equivalent/{equivalent}/destroy', 'EquivalentController@destroy')->name('equivalent.destroy');
                Route::post('/equivalent/{equivalent}/json-products', 'EquivalentController@json_products')->name('equivalent.json-products');

                //Группа товаров
                Route::group([
                    'prefix' => 'group',
                    'as' => 'group.',
                ], function() {
                    Route::post('/add-products/{group}', 'GroupController@add_products')->name('add-products');
                    Route::post('/add-product/{group}', 'GroupController@add_product')->name('add-product');
                    Route::delete('/del-product/{group}', 'GroupController@del_product')->name('del-product');
                    Route::post('/search/{group}', 'GroupController@search')->name('search');
                });



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


                Route::post('/{product}/file-upload', 'ProductController@file_upload')->name('file-upload');
                Route::post('/{product}/get-images', 'ProductController@get_images')->name('get-images');
                Route::post('/{product}/del-image', 'ProductController@del_image')->name('del-image');
                Route::post('/{product}/up-image', 'ProductController@up_image')->name('up-image');
                Route::post('/{product}/down-image', 'ProductController@down_image')->name('down-image');
                Route::post('/{product}/alt-image', 'ProductController@alt_image')->name('alt-image');
                Route::post('/search', 'ProductController@search')->name('search');
                Route::post('/search-add', 'ProductController@search_add')->name('search-add');
                Route::post('/search_bonus', 'ProductController@search_bonus')->name('search-bonus');
                Route::post('/{product}/attr-modification', 'ProductController@attr_modification')->name('attr-modification');
                Route::post('/toggle/{product}', 'ProductController@toggle')->name('toggle');
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
                Route::group([
                    'prefix' => 'promotion',
                    'as' => 'promotion.',
                ], function () {
                    //Route::post('/{promotion}/add-group', 'PromotionController@add_group')->name('add-group');
                    Route::post('/add-product/{promotion}', 'PromotionController@add_product')->name('add-product');
                    Route::post('/add-products/{promotion}', 'PromotionController@add_products')->name('add-products');
                    Route::post('/search/{promotion}', 'PromotionController@search')->name('search');
                    Route::post('/{promotion}/set-product/{product}', 'PromotionController@set_product')->name('set-product');
                    Route::delete('/{promotion}/del-product/{product}', 'PromotionController@del_product')->name('del-product');

                    Route::delete('/{promotion}/del-group/{group}', 'PromotionController@del_group')->name('del-group');
                    Route::post('/published/{promotion}', 'PromotionController@published')->name('published');
                    Route::post('/draft/{promotion}', 'PromotionController@draft')->name('draft');
                    Route::post('/stop/{promotion}', 'PromotionController@stop')->name('stop');
                    Route::post('/start/{promotion}', 'PromotionController@start')->name('start');
                });



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

                Route::get('/calendar', 'CalendarController@index')->name('calendar.index');
                Route::get('/calendar/schedule', 'CalendarController@schedule')->name('calendar.schedule');
                Route::post('/assembling/{expense}', 'DeliveryController@assembling')->name('assembling');
                Route::post('/delivery/{expense}', 'DeliveryController@delivery')->name('delivery');
                Route::post('/completed/{expense}', 'DeliveryController@completed')->name('completed');

                Route::resource('truck', 'TruckController');
                Route::post('/truck/toggle/{truck}', 'TruckController@toggle')->name('truck.toggle');
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

                Route::resource('order', 'OrderController');
                Route::resource('payment', 'PaymentController');
                //Заказы
                Route::group(
                    [
                        'prefix' => 'order',
                        'as' => 'order.',
                        //'namespace' => '',
                    ],
                    function () {
                        Route::post('/copy/{order}', 'OrderController@copy')->name('copy');
                        //Route::delete('/destroy/{order}', 'OrderController@destroy')->name('destroy');
                        Route::post('/movement/{order}', 'OrderController@movement')->name('movement');
                        Route::post('/expense-calculate/{order}', 'OrderController@expense_calculate')->name('expense-calculate');
                        Route::post('/invoice/{order}', 'OrderController@invoice')->name('invoice');
                        Route::post('/send-invoice/{order}', 'OrderController@send_invoice')->name('send-invoice');
                        Route::post('/resend-invoice/{order}', 'OrderController@resend_invoice')->name('resend-invoice');

                        Route::post('/set-manager/{order}', 'OrderController@set_manager')->name('set-manager');
                        Route::post('/set-reserve/{order}', 'OrderController@set_reserve')->name('set-reserve');

                        Route::post('/canceled/{order}', 'OrderController@canceled')->name('canceled');
                        Route::post('/set-awaiting/{order}', 'OrderController@set_awaiting')->name('set-awaiting');

                        Route::post('/search-user', 'OrderController@search_user')->name('search-user');
                        Route::post('/search', 'OrderController@search')->name('search');
                        //Route::post('/get-to-order', 'OrderController@get_to_order')->name('get-to-order');

                        Route::get('/log/{order}', 'OrderController@log')->name('log');
                        Route::post('/take/{order}', 'OrderController@take')->name('take');
                    }
                );
                //Распоряжения
                Route::group(
                    [
                        'prefix' => 'expense',
                        'as' => 'expense.',
                    ],
                    function () {
                        Route::post('/create', 'ExpenseController@create')->name('create');
                        Route::post('/issue_shop', 'ExpenseController@issue_shop')->name('issue-shop');
                        Route::post('/issue_warehouse', 'ExpenseController@issue_warehouse')->name('issue-warehouse');
                        Route::get('/show/{expense}', 'ExpenseController@show')->name('show');
                        Route::delete('/destroy/{expense}', 'ExpenseController@destroy')->name('destroy');
                        Route::post('/assembly/{expense}', 'ExpenseController@assembly')->name('assembly');
                        Route::post('/trade12/{expense}', 'ExpenseController@trade12')->name('trade12');
                    }
                );
                //Возвраты
                Route::group(
                    [
                        'prefix' => 'refund',
                        'as' => 'refund.',
                    ],
                    function () {
                        Route::get('/index', 'RefundController@index')->name('index');
                        Route::get('/show/{refund}', 'RefundController@show')->name('show');
                        Route::get('/create', 'RefundController@create')->name('create');
                        Route::post('/store/{order}', 'RefundController@store')->name('store');
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

                Route::resource('contact', 'ContactController')->except(['show']); //CRUD
                Route::post('/contact/{contact}/draft', 'ContactController@draft')->name('contact.draft');
                Route::post('/contact/{contact}/published', 'ContactController@published')->name('contact.published');
                Route::post('/contact/{contact}/up', 'ContactController@up')->name('contact.up');
                Route::post('/contact/{contact}/down', 'ContactController@down')->name('contact.down');
            }
        );

        //ACCOUNTING
        Route::group(
            [
                'prefix' => 'accounting',
                'as' => 'accounting.',
                'namespace' => 'Accounting',
            ],
            function () {
                Route::group([
                    'prefix' => 'arrival',
                    'as' => 'arrival.',
                ],
                    function () {
                        Route::post('/search/{arrival}', 'ArrivalController@search')->name('search');
                        Route::post('/add-products/{arrival}', 'ArrivalController@add_products')->name('add-products');
                        Route::post('/add/{arrival}', 'ArrivalController@add')->name('add');

                        Route::post('/completed/{arrival}', 'ArrivalController@completed')->name('completed');
                        Route::post('/set/{item}', 'ArrivalController@set')->name('set');
                        Route::delete('/remove-item/{item}', 'ArrivalController@remove_item')->name('remove-item');
                    });
                Route::group([
                    'prefix' => 'movement',
                    'as' => 'movement.',
                ],
                    function () {
                        Route::post('/search/{movement}', 'MovementController@search')->name('search');
                        Route::post('/add/{movement}', 'MovementController@add')->name('add');
                        Route::post('/add-products/{movement}', 'MovementController@add_products')->name('add-products');

                        Route::post('/activate/{movement}', 'MovementController@activate')->name('activate');
                        Route::post('/departure/{movement}', 'MovementController@departure')->name('departure');
                        Route::post('/arrival/{movement}', 'MovementController@arrival')->name('arrival');
                        Route::post('/set/{item}', 'MovementController@set')->name('set');
                        Route::delete('/remove-item/{item}', 'MovementController@remove_item')->name('remove-item');
                    });
                Route::group([
                    'prefix' => 'departure',
                    'as' => 'departure.',
                ],
                    function () {
                        Route::post('/search/{departure}', 'DepartureController@search')->name('search');
                        Route::post('/add/{departure}', 'DepartureController@add')->name('add');
                        Route::post('/add-products/{departure}', 'DepartureController@add_products')->name('add-products');
                        Route::post('/completed/{departure}', 'DepartureController@completed')->name('completed');
                        Route::post('/set/{item}', 'DepartureController@set')->name('set');
                        Route::delete('/remove-item/{item}', 'DepartureController@remove_item')->name('remove-item');
                    });
                Route::group([
                    'prefix' => 'supply',
                    'as' => 'supply.',
                ],
                    function () {
                        Route::get('/stack', 'SupplyController@stack')->name('stack');
                        Route::delete('/del-stack/{stack}', 'SupplyController@del_stack')->name('del-stack');
                        Route::post('/add-stack/{item}', 'SupplyController@add_stack')->name('add-stack');
                        Route::post('/add-product/{supply}', 'SupplyController@add_product')->name('add-product');
                        Route::post('/add-products/{supply}', 'SupplyController@add_products')->name('add-products');
                        Route::post('/set-product/{product}', 'SupplyController@set_product')->name('set-product');
                        Route::delete('/del-product/{product}', 'SupplyController@del_product')->name('del-product');
                        Route::post('/search/{supply}', 'SupplyController@search')->name('search');
                        Route::post('/sent/{supply}', 'SupplyController@sent')->name('sent');
                        Route::post('/completed/{supply}', 'SupplyController@completed')->name('completed');

                    });
                Route::group([
                    'prefix' => 'pricing',
                    'as' => 'pricing.',
                ],
                    function () {
                        Route::post('/search/{pricing}', 'PricingController@search')->name('search');
                        Route::post('/add/{pricing}', 'PricingController@add')->name('add');
                        Route::post('/add-products/{pricing}', 'PricingController@add_products')->name('add-products');
                        Route::post('/completed/{pricing}', 'PricingController@completed')->name('completed');
                        Route::post('/create-arrival/{arrival}', 'PricingController@create_arrival')->name('create-arrival');
                        Route::post('/set/{item}', 'PricingController@set')->name('set');
                        Route::delete('/remove-item/{item}', 'PricingController@remove_item')->name('remove-item');
                    });

                Route::resource('storage', 'StorageController')->except(['destroy']); //CRUD
                Route::resource('distributor', 'DistributorController'); //CRUD
                Route::resource('currency', 'CurrencyController'); //CRUD
                Route::resource('arrival', 'ArrivalController')->except(['create', 'edit', 'update']); //CRUD
                Route::resource('movement', 'MovementController')->except(['create', 'edit', 'update']); //CRUD
                Route::resource('departure', 'DepartureController')->except(['create', 'edit', 'update']); //CRUD
                Route::resource('supply', 'SupplyController')->except(['edit', 'update']); //CRUD
                Route::resource('pricing', 'PricingController')->except(['store', 'edit', 'update']); //CRUD
                Route::resource('organization', 'OrganizationController'); //CRUD

            }
        );

        //ANALYTICS
        Route::group(
            [
                'prefix' => 'analytics',
                'as' => 'analytics.',
                'namespace' => 'Analytics',
            ],
            function () {
                Route::resource('cron', 'CronController')->only(['index', 'show']); //CRUD
                Route::resource('activity', 'ActivityController')->only(['index']); //CRUD

            }
        );
        //FEEDBACK
        Route::group([
            'prefix' => 'feedback',
            'as' => 'feedback.',
            'namespace' => 'Feedback',
        ], function() {

            Route::get('/review', 'ReviewController@index')->name('review.index');
            Route::get('/review/{review}', 'ReviewController@show')->name('review.show');
            Route::post('/review/{review}/published', 'ReviewController@published')->name('review.published');
            Route::post('/review/{review}/blocked', 'ReviewController@blocked')->name('review.blocked');


        });

        //AJAX Product




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
