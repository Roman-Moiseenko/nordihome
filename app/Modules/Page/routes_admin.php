<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'page',
        'as' => 'page.',
       // 'namespace' => 'Page',
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
