<?php

use Illuminate\Http\Request;

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

Route::post('/broadcasting/auth', function (Request $request) {
    return response()->json([], 200);
})->middleware('auth');

Route::post('/users', 'Auth\AuthController@register');
Route::post('/auth', 'Auth\AuthController@auth');
