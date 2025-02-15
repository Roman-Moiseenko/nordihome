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

            Route::post('/add-category', 'CategoryParserController@add_category')->name('add-category');
            Route::post('/toggle/{category}', 'CategoryParserController@toggle')->name('toggle');
            Route::get('/child/{category}', 'CategoryParserController@child')->name('child');
            Route::get('/{category}', 'CategoryParserController@show')->name('show');
            Route::delete('/{category}', 'CategoryParserController@destroy')->name('destroy');
            Route::post('/set-category/{category}', 'CategoryParserController@set_category')->name('set-category');
            Route::post('/', 'CategoryParserController@store')->name('store');
            Route::post('/parser-products/{category}', 'CategoryParserController@parser_products')->name('parser-products');
            Route::post('/parser-product/{category}', 'CategoryParserController@parser_product')->name('parser-product');

            Route::get('/', 'CategoryParserController@index')->name('index');

         //   Route::post('/list', 'CategoryParserController@list')->name('list');
        //    Route::post('/set-info/{category}', 'CategoryParserController@set_info')->name('set-info');
        });

        Route::group([
            'prefix' => 'product',
            'as' => 'product.',
        ], function () {
            Route::post('/parser/{product}', 'ProductParserController@parser')->name('parser');

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


