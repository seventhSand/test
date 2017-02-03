<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

// !!!"Webarq" routing should be load in the end
include 'webarq.php';

if (config('elfinder.route.prefix') !== Request::segment(1)) {
    Route::group(['prefix' => config('webarq.system.panel-url-prefix', 'admin-cp'), 'middleware' => 'panel'], function () {
        webarqAutoRoute('Panel');
    });

    webarqAutoRoute('Site');
}


