<?php

use Illuminate\Support\Facades\Route;

Route::get('/settings', 'SettingController@index')->name('setting.index');

Route::get('/setting/parser', 'SettingController@parser')->name('setting.parser');

Route::get('/setting/common', 'SettingController@common')->name('setting.common');
Route::get('/setting/coupon', 'SettingController@coupon')->name('setting.coupon');
Route::get('/setting/web', 'SettingController@web')->name('setting.web');
Route::get('/setting/mail', 'SettingController@mail')->name('setting.mail');
Route::get('/setting/notification', 'SettingController@notification')->name('setting.notification');
Route::get('/setting/image', 'SettingController@image')->name('setting.image');

Route::put('/setting', 'SettingController@update')->name('setting.update');



