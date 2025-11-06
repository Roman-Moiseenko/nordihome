<?php

use App\Modules\Accounting\Controllers\ArrivalController;
use App\Modules\Accounting\Controllers\CurrencyController;
use App\Modules\Accounting\Controllers\DepartureController;
use App\Modules\Accounting\Controllers\DistributorController;
use App\Modules\Accounting\Controllers\InventoryController;
use App\Modules\Accounting\Controllers\MovementController;
use App\Modules\Accounting\Controllers\OrganizationController;
use App\Modules\Accounting\Controllers\PaymentController;
use App\Modules\Accounting\Controllers\PricingController;
use App\Modules\Accounting\Controllers\RefundController;
use App\Modules\Accounting\Controllers\StorageController;
use App\Modules\Accounting\Controllers\SupplyController;
use App\Modules\Accounting\Controllers\SurplusController;
use App\Modules\Accounting\Controllers\TraderController;
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
                Route::post('/set-product/{product}', [ArrivalController::class,'set_product'])->name('set-product');
                Route::delete('/del-product/{product}', [ArrivalController::class, 'del_product'])->name('del-product');

                Route::post('/add-products/{arrival}', [ArrivalController::class, 'add_products'])->name('add-products');
                Route::post('/add-product/{arrival}', [ArrivalController::class, 'add_product'])->name('add-product');
                Route::post('/set-info/{arrival}', [ArrivalController::class, 'set_info'])->name('set-info');
                Route::post('/completed/{arrival}', [ArrivalController::class, 'completed'])->name('completed');
                Route::post('/work/{arrival}', [ArrivalController::class, 'work'])->name('work');
                //На основании:
                Route::post('/expense/{arrival}', [ArrivalController::class, 'expense'])->name('expense'); //Доп.расходы
                Route::post('/movement/{arrival}', [ArrivalController::class, 'movement'])->name('movement'); //Перемещение
                Route::post('/pricing/{arrival}', [ArrivalController::class, 'pricing'])->name('pricing'); //Установка цен
                Route::post('/refund/{arrival}', [ArrivalController::class, 'refund'])->name('refund'); //Возврат
                //Мягкое удаление
                Route::delete('/full-destroy/{arrival}', [ArrivalController::class, 'full_destroy'])->name('full-destroy');
                Route::post('/restore/{arrival}', [ArrivalController::class, 'restore'])->name('restore');


                //Доп.расходы
                Route::group([
                    'prefix' => 'expense',
                    'as' => 'expense.',
                ], function () {
                    Route::get('/view/{expense}', [ArrivalController::class, 'expense_show'])->name('show'); //Доп.расходы
                    Route::post('/set-info/{expense}', [ArrivalController::class, 'expense_set_info'])->name('set-info'); //Доп.расходы
                    Route::post('/add-item/{expense}', [ArrivalController::class, 'expense_add_item'])->name('add-item'); //Доп.расходы
                    Route::post('/set-item/{item}', [ArrivalController::class, 'expense_set_item'])->name('set-item'); //Доп.расходы
                    Route::delete('/del-item/{item}', [ArrivalController::class, 'expense_del_item'])->name('del-item'); //Доп.расходы
                    Route::delete('/destroy/{expense}', [ArrivalController::class, 'expense_destroy'])->name('destroy'); //Доп.расходы
                    //Мягкое удаление
                    Route::delete('/full-destroy/{expense}', [ArrivalController::class, 'expense_full_destroy'])->name('full-destroy');
                    Route::post('/restore/{expense}', [ArrivalController::class, 'expense_restore'])->name('restore');
                });


            });
        //MOVEMENT
        Route::group([
            'prefix' => 'movement',
            'as' => 'movement.',
        ],
            function () {
                Route::post('/set-product/{product}', [MovementController::class, 'set_product'])->name('set-product');
                Route::delete('/del-product/{product}', [MovementController::class, 'del_product'])->name('del-product');

                Route::post('/add-products/{movement}', [MovementController::class, 'add_products'])->name('add-products');
                Route::post('/add-product/{movement}', [MovementController::class, 'add_product'])->name('add-product');
                Route::post('/set-info/{movement}', [MovementController::class, 'set_info'])->name('set-info');
                Route::post('/completed/{movement}', [MovementController::class, 'completed'])->name('completed');
                Route::post('/work/{movement}', [MovementController::class, 'work'])->name('work');

                Route::post('/departure/{movement}', [MovementController::class, 'departure'])->name('departure');
                Route::post('/arrival/{movement}', [MovementController::class, 'arrival'])->name('arrival');
                //Мягкое удаление
                Route::delete('/full-destroy/{movement}', [MovementController::class, 'full_destroy'])->name('full-destroy');
                Route::post('/restore/{movement}', [MovementController::class, 'restore'])->name('restore');
            });
        //INVENTORY
        Route::group([
            'prefix' => 'inventory',
            'as' => 'inventory.',
        ],
            function () {
                Route::post('/set-product/{product}', [InventoryController::class, 'set_product'])->name('set-product');
                Route::delete('/del-product/{product}', [InventoryController::class, 'del_product'])->name('del-product');

                Route::post('/add-products/{inventory}', [InventoryController::class, 'add_products'])->name('add-products');
                Route::post('/add-product/{inventory}', [InventoryController::class, 'add_product'])->name('add-product');
                Route::post('/set-info/{inventory}', [InventoryController::class, 'set_info'])->name('set-info');
                Route::post('/completed/{inventory}', [InventoryController::class, 'completed'])->name('completed');
                Route::post('/work/{inventory}', [InventoryController::class, 'work'])->name('work');
                //Мягкое удаление
                Route::delete('/full-destroy/{inventory}', [InventoryController::class, 'full_destroy'])->name('full-destroy');
                Route::post('/restore/{inventory}', [InventoryController::class, 'restore'])->name('restore');
            });
        //SURPLUS
        Route::group([
            'prefix' => 'surplus',
            'as' => 'surplus.',
        ],
            function () {
                Route::post('/add-product/{surplus}', [SurplusController::class, 'add_product'])->name('add-product');
                Route::post('/add-products/{surplus}', [SurplusController::class, 'add_products'])->name('add-products');
                Route::post('/completed/{surplus}', [SurplusController::class, 'completed'])->name('completed');
                Route::post('/work/{surplus}', [SurplusController::class, 'work'])->name('work');

                Route::post('/set-product/{product}', [SurplusController::class, 'set_product'])->name('set-product');
                Route::delete('/del-product/{product}', [SurplusController::class, 'del_product'])->name('del-product');
                Route::post('/set-info/{surplus}', [SurplusController::class, 'set_info'])->name('set-info');
                Route::get('/', [SurplusController::class, 'index'])->name('index');
                Route::post('/', [SurplusController::class, 'store'])->name('store');
                Route::get('/{surplus}', [SurplusController::class, 'show'])->name('show');
                Route::delete('/destroy/{surplus}', [SurplusController::class, 'destroy'])->name('destroy');
                //Мягкое удаление
                Route::delete('/full-destroy/{surplus}', [SurplusController::class, 'full_destroy'])->name('full-destroy');
                Route::post('/restore/{surplus}', [SurplusController::class, 'restore'])->name('restore');

            });
        //DEPARTURE
        Route::group([
            'prefix' => 'departure',
            'as' => 'departure.',
        ],
            function () {
                Route::post('/add-product/{departure}', [DepartureController::class, 'add_product'])->name('add-product');
                Route::post('/add-products/{departure}', [DepartureController::class, 'add_products'])->name('add-products');
                Route::post('/completed/{departure}', [DepartureController::class, 'completed'])->name('completed');
                Route::post('/work/{departure}', [DepartureController::class, 'work'])->name('work');
                Route::post('/set-product/{product}', [DepartureController::class, 'set_product'])->name('set-product');
                Route::delete('/del-product/{product}', [DepartureController::class, 'del_product'])->name('del-product');
                Route::post('/set-info/{departure}', [DepartureController::class, 'set_info'])->name('set-info');
                Route::post('/upload/{departure}', [DepartureController::class, 'upload'])->name('upload');
                Route::post('/delete-photo/{departure}', [DepartureController::class, 'delete_photo'])->name('delete-photo');
                //Мягкое удаление
                Route::delete('/full-destroy/{departure}', [DepartureController::class, 'full_destroy'])->name('full-destroy');
                Route::post('/restore/{departure}', [DepartureController::class, 'restore'])->name('restore');
        });
        //DISTRIBUTION
        Route::group([
            'prefix' => 'distributor',
            'as' => 'distributor.',
        ],
            function () {
                Route::post('/supply/{distributor}', [DistributorController::class, 'supply'])->name('supply');
                Route::post('/attach/{distributor}', [DistributorController::class, 'attach'])->name('attach');
                Route::post('/detach/{distributor}', [DistributorController::class, 'detach'])->name('detach');
                Route::post('/default/{distributor}', [DistributorController::class, 'default'])->name('default');
                Route::post('/set-info/{distributor}', [DistributorController::class, 'set_info'])->name('set-info');
            });
        //TRADER
        Route::group([
            'prefix' => 'trader',
            'as' => 'trader.',
        ],
            function () {
                Route::post('/attach/{trader}', [TraderController::class, 'attach'])->name('attach');
                Route::post('/detach/{trader}', [TraderController::class, 'detach'])->name('detach');
                Route::post('/default/{trader}', [TraderController::class, 'default'])->name('default');
                Route::post('/set-info/{trader}', [TraderController::class, 'set_info'])->name('set-info');
            });
        //SUPPLY
        Route::group([
            'prefix' => 'supply',
            'as' => 'supply.',
        ],
            function () {
                //На основании:
                Route::post('/arrival/{supply}', [SupplyController::class, 'arrival'])->name('arrival');
                Route::post('/payment/{supply}', [SupplyController::class, 'payment'])->name('payment');

                Route::get('/stack', [SupplyController::class, 'stack'])->name('stack');
                Route::delete('/del-stack/{stack}', [SupplyController::class, 'del_stack'])->name('del-stack');
                Route::post('/add-stack/{item}', [SupplyController::class, 'add_stack'])->name('add-stack');
                Route::post('/add-product/{supply}', [SupplyController::class, 'add_product'])->name('add-product');
                Route::post('/add-products/{supply}', [SupplyController::class, 'add_products'])->name('add-products');
                Route::post('/set-product/{product}', [SupplyController::class, 'set_product'])->name('set-product');
                Route::delete('/del-product/{product}', [SupplyController::class, 'del_product'])->name('del-product');

                Route::post('/copy/{supply}', [SupplyController::class, 'copy'])->name('copy');
                Route::post('/completed/{supply}', [SupplyController::class, 'completed'])->name('completed');
                Route::post('/work/{supply}', [SupplyController::class, 'work'])->name('work');
                Route::post('/set-info/{supply}', [SupplyController::class, 'set_info'])->name('set-info');
                //Мягкое удаление
                Route::delete('/full-destroy/{supply}', [SupplyController::class, 'full_destroy'])->name('full-destroy');
                Route::post('/restore/{supply}', [SupplyController::class, 'restore'])->name('restore');


            });
        //PRICING
        Route::group([
            'prefix' => 'pricing',
            'as' => 'pricing.',
        ],
            function () {
                Route::post('/set-product/{product}', [PricingController::class, 'set_product'])->name('set-product');
                Route::delete('/del-product/{product}', [PricingController::class, 'del_product'])->name('del-product');

                Route::post('/add-products/{pricing}', [PricingController::class, 'add_products'])->name('add-products');
                Route::post('/add-product/{pricing}', [PricingController::class, 'add_product'])->name('add-product');
                Route::post('/set-info/{pricing}', [PricingController::class, 'set_info'])->name('set-info');
                Route::post('/completed/{pricing}', [PricingController::class, 'completed'])->name('completed');
                Route::post('/work/{pricing}', [PricingController::class, 'work'])->name('work');
                Route::post('/copy/{pricing}', [PricingController::class, 'copy'])->name('copy');
                //Мягкое удаление
                Route::delete('/full-destroy/{pricing}', [PricingController::class, 'full_destroy'])->name('full-destroy');
                Route::post('/restore/{pricing}', [PricingController::class, 'restore'])->name('restore');
            });
        //ORGANIZATION
        Route::group([
            'prefix' => 'organization',
            'as' => 'organization.',
        ],
            function () {
                Route::delete('/del-contact/{contact}', [OrganizationController::class, 'del_contact'])->name('del-contact');
                Route::post('/set-contact/{organization}', [OrganizationController::class, 'set_contact'])->name('set-contact');
                Route::post('/update/{organization}', [OrganizationController::class, 'update'])->name('update');
                Route::post('/set-info/{organization}', [OrganizationController::class, 'set_info'])->name('set-info');
                Route::post('/search-add', [OrganizationController::class, 'search_add'])->name('search-add');
                Route::post('/find', [OrganizationController::class, 'find'])->name('find');
                Route::post('/upload/{organization}', [OrganizationController::class, 'upload'])->name('upload');
            });
        //PAYMENT
        Route::group([
            'prefix' => 'payment',
            'as' => 'payment.'
        ],
            function () {
                //Route::post('/create', [PaymentController::class, 'create'])->name('create');
                Route::post('/completed/{payment}', [PaymentController::class, 'completed'])->name('completed');
                Route::post('/work/{payment}', [PaymentController::class, 'work'])->name('work');
                //Route::post('/upload', [PaymentController::class, 'upload'])->name('upload');

                Route::post('/set-info/{payment}', [PaymentController::class, 'set_info'])->name('set-info');
                Route::post('/not-paid/{payment}', [PaymentController::class, 'not_paid'])->name('not-paid');
                Route::post('/set-amount/{decryption}', [PaymentController::class, 'set_amount'])->name('set-amount');
                //Мягкое удаление
                Route::delete('/full-destroy/{payment}', [PaymentController::class, 'full_destroy'])->name('full-destroy');
                Route::post('/restore/{payment}', [PaymentController::class, 'restore'])->name('restore');
        });
        //BANK
        /*
        Route::group([
            'prefix' => 'bank',
            'as' => 'bank.'
        ],
            function () {
                Route::post('/upload', 'BankController@upload')->name('upload');
                Route::post('/currency', 'BankController@currency')->name('currency');
            });
        */
        //STORAGE
        Route::group([
            'prefix' => 'storage',
            'as' => 'storage.'
        ],
            function () {
                Route::post('/set-info/{storage}', [StorageController::class, 'set_info'])->name('set-info');
            });
        //REFUND
        Route::group([
            'prefix' => 'refund',
            'as' => 'refund.',
        ],
            function () {
                Route::post('/set-product/{product}', [RefundController::class, 'set_product'])->name('set-product');
                Route::delete('/del-product/{product}', [RefundController::class, 'del_product'])->name('del-product');

                Route::post('/add-products/{refund}', [RefundController::class, 'add_products'])->name('add-products');
                Route::post('/add-product/{refund}', [RefundController::class, 'add_product'])->name('add-product');
                Route::post('/set-info/{refund}', [RefundController::class, 'set_info'])->name('set-info');
                Route::post('/completed/{refund}', [RefundController::class, 'completed'])->name('completed');
                Route::post('/work/{refund}', [RefundController::class, 'work'])->name('work');
                //На основании:
                //Мягкое удаление
                Route::delete('/full-destroy/{refund}', [RefundController::class, 'full_destroy'])->name('full-destroy');
                Route::post('/restore/{refund}', [RefundController::class, 'restore'])->name('restore');
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
