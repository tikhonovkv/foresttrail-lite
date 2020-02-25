<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| App client Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', 'SiteController@appClient')->name('app');
Route::get('/{id}', 'Api\ShareController@getTrackers');

//include('api.php');