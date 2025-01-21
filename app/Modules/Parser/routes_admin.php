<?php
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'parser',
        'as' => 'parser.',
    ],
    function () {

        //CATEGORY
        Route::group([
            'prefix' => 'category',
            'as' => 'category.',
        ], function () {



            Route::post('/toggle/{category}', 'CategoryController@toggle')->name('toggle');
            Route::get('/child/{category}', 'CategoryController@child')->name('child');
            Route::get('/{category}', 'CategoryController@show')->name('show');
            Route::delete('/{category}', 'CategoryController@destroy')->name('destroy');

            Route::post('/', 'CategoryController@store')->name('store');

            Route::get('/', 'CategoryController@index')->name('index');

         //   Route::post('/list', 'CategoryController@list')->name('list');
        //    Route::post('/set-info/{category}', 'CategoryController@set_info')->name('set-info');
        });


        //PRODUCT
 /*       Route::group([
            'prefix' => 'image',
            'as' => 'image.',
        ], function (){
            Route::post('/add/{product}', 'ProductController@add_image')->name('add');
            Route::post('/get/{product}', 'ProductController@get_images')->name('get');
            Route::delete('/del/{product}', 'ProductController@del_image')->name('del');
            Route::post('/up/{product}', 'ProductController@up_image')->name('up');
            Route::post('/down/{product}', 'ProductController@down_image')->name('down');
            Route::post('/set/{product}', 'ProductController@set_image')->name('set');
            Route::post('/move/{product}', 'ProductController@move_image')->name('move');
        });
        */

    }
);


