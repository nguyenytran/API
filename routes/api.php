<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

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

// authorization
Route::post('authorization', 'AuthorizationController@store');
Route::delete('authorization', 'AuthorizationController@destroy');

// register
Route::post('register', 'RegisterUserController@store');

Route::group(['middleware' => ['auth:api']], function (Router $router) {
    // users
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/me', ['uses' => 'UserController@show']);
        $router->put('/change-password', ['uses' => 'UserController@changePassword']);
        $router->post('/', [
            'uses' => 'UserController@store',
            'middleware' => ['role_or_permission:admin|create users']
        ]);
        $router->get('/', [
            'uses' => 'UserController@index',
            'middleware' => ['role_or_permission:admin|read users']
        ]);
        $router->get('{id}', [
            'uses' => 'UserController@show',
            'middleware' => ['role_or_permission:admin|read users']
        ]);
        $router->put('/{id}/change-password', [
            'uses' => 'UserController@changePasswordForAdmin',
            'middleware' => ['role:admin']
        ]);
        $router->put('{id}', [
            'uses' => 'UserController@update',
            'middleware' => ['role_or_permission:admin|update users']
        ]);
        $router->delete('{id}', [
            'uses' => 'UserController@destroy',
            'middleware' => ['role_or_permission:admin|delete users']
        ]);
        $router->delete('{id}/logout', [
            'uses' => 'UserController@logout',
            'middleware' => ['role:admin']
        ]);
    });
});
