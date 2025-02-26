<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'accounting',
        'as' => 'accounting.',
    ],
    function () {
        //ARRIVAL
        Route::group([
            'prefix' => 'arrival',
            'as' => 'arrival.',
        ],
            function () {
                Route::post('/set-product/{product}', 'ArrivalController@set_product')->name('set-product');
                Route::delete('/del-product/{product}', 'ArrivalController@del_product')->name('del-product');

                Route::post('/add-products/{arrival}', 'ArrivalController@add_products')->name('add-products');
                Route::post('/add-product/{arrival}', 'ArrivalController@add_product')->name('add-product');
                Route::post('/set-info/{arrival}', 'ArrivalController@set_info')->name('set-info');
                Route::post('/completed/{arrival}', 'ArrivalController@completed')->name('completed');
                Route::post('/work/{arrival}', 'ArrivalController@work')->name('work');
                //На основании:
                Route::post('/expense/{arrival}', 'ArrivalController@expense')->name('expense'); //Доп.расходы
                Route::post('/movement/{arrival}', 'ArrivalController@movement')->name('movement'); //Перемещение
                Route::post('/pricing/{arrival}', 'ArrivalController@pricing')->name('pricing'); //Установка цен
                Route::post('/refund/{arrival}', 'ArrivalController@refund')->name('refund'); //Возврат

                //Доп.расходы
                Route::get('/expense/view/{expense}', 'ArrivalController@expense_show')->name('expense.show'); //Доп.расходы
                Route::post('/expense/set-info/{expense}', 'ArrivalController@expense_set_info')->name('expense.set-info'); //Доп.расходы
                Route::post('/expense/add-item/{expense}', 'ArrivalController@expense_add_item')->name('expense.add-item'); //Доп.расходы
                Route::post('/expense/set-item/{item}', 'ArrivalController@expense_set_item')->name('expense.set-item'); //Доп.расходы
                Route::delete('/expense/del-item/{item}', 'ArrivalController@expense_del_item')->name('expense.del-item'); //Доп.расходы
                Route::delete('/expense/destroy/{expense}', 'ArrivalController@expense_destroy')->name('expense.destroy'); //Доп.расходы

            });
        //MOVEMENT
        Route::group([
            'prefix' => 'movement',
            'as' => 'movement.',
        ],
            function () {
                Route::post('/set-product/{product}', 'MovementController@set_product')->name('set-product');
                Route::delete('/del-product/{product}', 'MovementController@del_product')->name('del-product');

                Route::post('/add-products/{movement}', 'MovementController@add_products')->name('add-products');
                Route::post('/add-product/{movement}', 'MovementController@add_product')->name('add-product');
                Route::post('/set-info/{movement}', 'MovementController@set_info')->name('set-info');
                Route::post('/completed/{movement}', 'MovementController@completed')->name('completed');
                Route::post('/work/{movement}', 'MovementController@work')->name('work');

                Route::post('/departure/{movement}', 'MovementController@departure')->name('departure');
                Route::post('/arrival/{movement}', 'MovementController@arrival')->name('arrival');
            });
        //INVENTORY
        Route::group([
            'prefix' => 'inventory',
            'as' => 'inventory.',
        ],
            function () {
                Route::post('/set-product/{product}', 'InventoryController@set_product')->name('set-product');
                Route::delete('/del-product/{product}', 'InventoryController@del_product')->name('del-product');

                Route::post('/add-products/{inventory}', 'InventoryController@add_products')->name('add-products');
                Route::post('/add-product/{inventory}', 'InventoryController@add_product')->name('add-product');
                Route::post('/set-info/{inventory}', 'InventoryController@set_info')->name('set-info');
                Route::post('/completed/{inventory}', 'InventoryController@completed')->name('completed');
                Route::post('/work/{inventory}', 'InventoryController@work')->name('work');
            });
        //SURPLUS
        Route::group([
            'prefix' => 'surplus',
            'as' => 'surplus.',
        ],
            function () {
                Route::post('/add-product/{surplus}', 'SurplusController@add_product')->name('add-product');
                Route::post('/add-products/{surplus}', 'SurplusController@add_products')->name('add-products');
                Route::post('/completed/{surplus}', 'SurplusController@completed')->name('completed');
                Route::post('/work/{surplus}', 'SurplusController@work')->name('work');

                Route::post('/set-product/{product}', 'SurplusController@set_product')->name('set-product');
                Route::delete('/del-product/{product}', 'SurplusController@del_product')->name('del-product');
                Route::post('/set-info/{surplus}', 'SurplusController@set_info')->name('set-info');
                Route::get('/', 'SurplusController@index')->name('index');
                Route::post('/', 'SurplusController@store')->name('store');
                Route::get('/{surplus}', 'SurplusController@show')->name('show');
                Route::delete('/destroy/{surplus}', 'SurplusController@destroy')->name('destroy');

            });
        //DEPARTURE
        Route::group([
            'prefix' => 'departure',
            'as' => 'departure.',
        ],
            function () {
                Route::post('/add-product/{departure}', 'DepartureController@add_product')->name('add-product');
                Route::post('/add-products/{departure}', 'DepartureController@add_products')->name('add-products');
                Route::post('/completed/{departure}', 'DepartureController@completed')->name('completed');
                Route::post('/work/{departure}', 'DepartureController@work')->name('work');
                Route::post('/set-product/{product}', 'DepartureController@set_product')->name('set-product');
                Route::delete('/del-product/{product}', 'DepartureController@del_product')->name('del-product');
                Route::post('/set-info/{departure}', 'DepartureController@set_info')->name('set-info');
                Route::post('/upload/{departure}', 'DepartureController@upload')->name('upload');
                Route::post('/delete-photo/{departure}', 'DepartureController@delete_photo')->name('delete-photo');
            });
        //DISTRIBUTION
        Route::group([
            'prefix' => 'distributor',
            'as' => 'distributor.',
        ],
            function () {
                Route::post('/supply/{distributor}', 'DistributorController@supply')->name('supply');
                Route::post('/attach/{distributor}', 'DistributorController@attach')->name('attach');
                Route::post('/detach/{distributor}', 'DistributorController@detach')->name('detach');
                Route::post('/default/{distributor}', 'DistributorController@default')->name('default');
                Route::post('/set-info/{distributor}', 'DistributorController@set_info')->name('set-info');
            });
        //TRADER
        Route::group([
            'prefix' => 'trader',
            'as' => 'trader.',
        ],
            function () {
                Route::post('/attach/{trader}', 'TraderController@attach')->name('attach');
                Route::post('/detach/{trader}', 'TraderController@detach')->name('detach');
                Route::post('/default/{trader}', 'TraderController@default')->name('default');
                Route::post('/set-info/{trader}', 'TraderController@set_info')->name('set-info');
            });
        //SUPPLY
        Route::group([
            'prefix' => 'supply',
            'as' => 'supply.',
        ],
            function () {
                //На основании:
                Route::post('/arrival/{supply}', 'SupplyController@arrival')->name('arrival');
                Route::post('/payment/{supply}', 'SupplyController@payment')->name('payment');

                Route::get('/stack', 'SupplyController@stack')->name('stack');
                Route::delete('/del-stack/{stack}', 'SupplyController@del_stack')->name('del-stack');
                Route::post('/add-stack/{item}', 'SupplyController@add_stack')->name('add-stack');
                Route::post('/add-product/{supply}', 'SupplyController@add_product')->name('add-product');
                Route::post('/add-products/{supply}', 'SupplyController@add_products')->name('add-products');
                Route::post('/set-product/{product}', 'SupplyController@set_product')->name('set-product');
                Route::delete('/del-product/{product}', 'SupplyController@del_product')->name('del-product');

                Route::post('/copy/{supply}', 'SupplyController@copy')->name('copy');
                Route::post('/completed/{supply}', 'SupplyController@completed')->name('completed');
                Route::post('/work/{supply}', 'SupplyController@work')->name('work');
                Route::post('/set-info/{supply}', 'SupplyController@set_info')->name('set-info');

            });
        //PRICING
        Route::group([
            'prefix' => 'pricing',
            'as' => 'pricing.',
        ],
            function () {
                Route::post('/set-product/{product}', 'PricingController@set_product')->name('set-product');
                Route::delete('/del-product/{product}', 'PricingController@del_product')->name('del-product');

                Route::post('/add-products/{pricing}', 'PricingController@add_products')->name('add-products');
                Route::post('/add-product/{pricing}', 'PricingController@add_product')->name('add-product');
                Route::post('/set-info/{pricing}', 'PricingController@set_info')->name('set-info');
                Route::post('/completed/{pricing}', 'PricingController@completed')->name('completed');
                Route::post('/work/{pricing}', 'PricingController@work')->name('work');
                Route::post('/copy/{pricing}', 'PricingController@copy')->name('copy');
            });
        //ORGANIZATION
        Route::group([
            'prefix' => 'organization',
            'as' => 'organization.',
        ],
            function () {
                Route::delete('/del-contact/{contact}', 'OrganizationController@del_contact')->name('del-contact');
                Route::post('/set-contact/{organization}', 'OrganizationController@set_contact')->name('set-contact');
                Route::post('/update/{organization}', 'OrganizationController@update')->name('update');
                Route::post('/set-info/{organization}', 'OrganizationController@set_info')->name('set-info');
                Route::post('/search-add', 'OrganizationController@search_add')->name('search-add');
                Route::post('/find', 'OrganizationController@find')->name('find');
                Route::post('/upload/{organization}', 'OrganizationController@upload')->name('upload');
            });
        //PAYMENT
        Route::group([
            'prefix' => 'payment',
            'as' => 'payment.'
        ],
            function () {
                Route::post('/create', 'PaymentController@create')->name('create');
                Route::post('/completed/{payment}', 'PaymentController@completed')->name('completed');
                Route::post('/work/{payment}', 'PaymentController@work')->name('work');
                Route::post('/upload', 'PaymentController@upload')->name('upload');

                Route::post('/set-info/{payment}', 'PaymentController@set_info')->name('set-info');
                Route::post('/not-paid/{payment}', 'PaymentController@not_paid')->name('not-paid');
                Route::post('/set-amount/{decryption}', 'PaymentController@set_amount')->name('set-amount');
            });
        //BANK
        Route::group([
            'prefix' => 'bank',
            'as' => 'bank.'
        ],
            function () {
                Route::post('/upload', 'BankController@upload')->name('upload');
                Route::post('/currency', 'BankController@currency')->name('currency');
            });
        //STORAGE
        Route::group([
            'prefix' => 'storage',
            'as' => 'storage.'
        ],
            function () {
                Route::post('/set-info/{storage}', 'StorageController@set_info')->name('set-info');
            });
        //REFUND
        Route::group([
            'prefix' => 'refund',
            'as' => 'refund.',
        ],
            function () {
                Route::post('/set-product/{product}', 'RefundController@set_product')->name('set-product');
                Route::delete('/del-product/{product}', 'RefundController@del_product')->name('del-product');

                Route::post('/add-products/{refund}', 'RefundController@add_products')->name('add-products');
                Route::post('/add-product/{refund}', 'RefundController@add_product')->name('add-product');
                Route::post('/set-info/{refund}', 'RefundController@set_info')->name('set-info');
                Route::post('/completed/{refund}', 'RefundController@completed')->name('completed');
                Route::post('/work/{refund}', 'RefundController@work')->name('work');
                //На основании:

            });



        Route::resource('inventory', 'InventoryController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('refund', 'RefundController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('storage', 'StorageController')->except(['create', 'edit', 'update', 'destroy']); //CRUD
        Route::resource('distributor', 'DistributorController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('currency', 'CurrencyController')->except(['create', 'edit']); //CRUD
        Route::resource('arrival', 'ArrivalController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('movement', 'MovementController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('departure', 'DepartureController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('supply', 'SupplyController')->except(['edit', 'update']); //CRUD
        Route::resource('pricing', 'PricingController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('organization', 'OrganizationController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('trader', 'TraderController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('payment', 'PaymentController')->except(['create', 'store']); //CRUD
    }
);
