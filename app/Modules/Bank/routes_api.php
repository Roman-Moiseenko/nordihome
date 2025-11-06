<?php
declare(strict_types=1);


use App\Modules\Bank\Controllers\BankController;
use Illuminate\Support\Facades\Route;


Route::any('/bank/web-hook', [BankController::class, 'web_hook'])->name('bank.web-hook');
//Route::any('/bank/redirect', 'BankController@web_hook')->name('bank.web-hook');

