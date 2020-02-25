<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'SiteController@index');
Route::get('/app', 'SiteController@app')->name('app');
Route::get('/app-client', 'SiteController@appClient')->name('appClient');
//Auth::routes();
//Route::get('/home', 'HomeController@index')->name('home');

//\Illuminate\Support\Facades\DB::listen(function($query) { // вывод sql запроса на любой странице для отладки
//    dump($query->sql); //$query->bindings, $query->time
//});
