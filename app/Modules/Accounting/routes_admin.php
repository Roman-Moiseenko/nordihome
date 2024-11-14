<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'accounting',
        'as' => 'accounting.',
        //'namespace' => 'Accounting',
    ],
    function () {
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
                Route::post('/expenses/{arrival}', 'ArrivalController@expenses')->name('expenses'); //Доп.расходы
                Route::post('/movement/{arrival}', 'ArrivalController@movement')->name('movement'); //Перемещение
                Route::post('/invoice/{arrival}', 'ArrivalController@invoice')->name('invoice'); //Расх.накладная
                Route::post('/refund/{arrival}', 'ArrivalController@refund')->name('refund'); //Возврат
            });
        Route::group([
            'prefix' => 'movement',
            'as' => 'movement.',
        ],
            function () {
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
                Route::post('/add-product/{departure}', 'DepartureController@add_product')->name('add-product');
                Route::post('/add-products/{departure}', 'DepartureController@add_products')->name('add-products');
                Route::post('/completed/{departure}', 'DepartureController@completed')->name('completed');
                Route::post('/set-product/{product}', 'DepartureController@set_product')->name('set-product');
                Route::delete('/del-product/{product}', 'DepartureController@del_product')->name('del-product');
                Route::post('/set-info/{departure}', 'DepartureController@set_info')->name('set-info');

            });
        Route::group([
            'prefix' => 'distributor',
            'as' => 'distributor.',
        ],
            function () {
                Route::post('/supply/{distributor}', 'DistributorController@supply')->name('supply');
            });
        Route::group([
            'prefix' => 'supply',
            'as' => 'supply.',
        ],
            function () {
                //На основании:
                Route::post('/arrival/{supply}', 'SupplyController@arrival')->name('arrival');
                Route::post('/payment/{supply}', 'SupplyController@payment')->name('payment');
                //Route::post('/refund/{supply}', 'SupplyController@refund')->name('refund');

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
        Route::group([
            'prefix' => 'pricing',
            'as' => 'pricing.',
        ],
            function () {
                Route::post('/add/{pricing}', 'PricingController@add')->name('add');
                Route::post('/copy/{pricing}', 'PricingController@copy')->name('copy');
                Route::post('/add-products/{pricing}', 'PricingController@add_products')->name('add-products');
                Route::post('/completed/{pricing}', 'PricingController@completed')->name('completed');
                Route::post('/create-arrival/{arrival}', 'PricingController@create_arrival')->name('create-arrival');
                Route::post('/set/{item}', 'PricingController@set')->name('set');
                Route::delete('/remove-item/{item}', 'PricingController@remove_item')->name('remove-item');
            });

        Route::group([
            'prefix' => 'organization',
            'as' => 'organization.',
        ],
            function () {
                Route::post('/add-contact/{organization}', 'OrganizationController@add_contact')->name('add-contact');
                Route::post('/del-contact/{contact}', 'OrganizationController@del_contact')->name('del-contact');
                Route::post('/set-contact/{contact}', 'OrganizationController@set_contact')->name('set-contact');
                Route::get('/holdings', 'OrganizationController@holdings')->name('holdings');
                Route::post('/holding-detach/{organization}', 'OrganizationController@holding_detach')->name('holding-detach');
            });

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
                Route::post('/set-amount/{decryption}', 'PaymentController@set_amount')->name('set-amount');
            });

        Route::group([
            'prefix' => 'bank',
            'as' => 'bank.'
        ],
            function () {
                Route::post('/upload', 'BankController@upload')->name('upload');

            });

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

        Route::resource('refund', 'RefundController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('storage', 'StorageController')->except(['destroy']); //CRUD
        Route::resource('distributor', 'DistributorController'); //CRUD
        Route::resource('currency', 'CurrencyController'); //CRUD
        Route::resource('arrival', 'ArrivalController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('movement', 'MovementController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('departure', 'DepartureController')->except(['create', 'edit', 'update']); //CRUD
        Route::resource('supply', 'SupplyController')->except(['edit', 'update']); //CRUD
        Route::resource('pricing', 'PricingController')->except(['store', 'edit', 'update']); //CRUD
        Route::resource('organization', 'OrganizationController'); //CRUD
        Route::resource('trader', 'TraderController'); //CRUD
        Route::resource('payment', 'PaymentController')->except(['create', 'store']); //CRUD
    }
);
