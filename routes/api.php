<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return 'Hello world!';
});

Route::post('authorization', 'AuthorizationController@store');
Route::delete('authorization', 'AuthorizationController@destroy');

