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
    dd('lo');
//    Auth::attempt(['username' => 'superadmin']);
    return view('welcome');
});

Route::get('/form', function () {
    return view('webarq.samples.form');
});

Route::get('/list', function () {
    return view('webarq.samples.table');
});

// !!!"Webarq" routing should be load in the end
include 'webarq.php';