<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('register-user','UserCtrl@register_user');
Route::post('login-user','AuthCtrl@login_user');
Route::middleware('auth:api')->get('get-users','UserCtrl@get_users');
Route::middleware('auth:api')->get('logout','AuthCtrl@logout');
