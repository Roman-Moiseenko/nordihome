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
                Route::post('/sent/{supply}', 'SupplyController@sent')->name('sent');
                Route::post('/completed/{supply}', 'SupplyController@completed')->name('completed');

            });
        Route::group([
            'prefix' => 'pricing',
            'as' => 'pricing.',
        ],
            function () {
                Route::post('/add/{pricing}', 'PricingController@add')->name('add');
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
        Route::resource('trader', 'TraderController'); //CRUD
    }
);
