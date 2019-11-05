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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('add-plan', 'HiProject\PlanController@addPlan');
Route::prefix('wechat')->group(function () {
    Route::get('msg-receiver', 'Wechat\WechatController@msgReceiver');
    Route::post('msg-receiver', 'Wechat\WechatController@msgReceiver');
});
Route::middleware('ip_login')->prefix('hi-project')->group(function () {
    Route::get('ip-login', 'API\APIController@ipLogin');
    Route::post('ip-login', 'API\APIController@ipLogin');
});