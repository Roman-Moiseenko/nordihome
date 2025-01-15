<?php
declare(strict_types=1);


use Illuminate\Support\Facades\Route;


Route::any('/telegram/web-hook', 'TelegramController@web_hook')->name('telegram.web-hook');

