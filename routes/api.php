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

Route::prefix('wechat')->group(function () {
    Route::get('msg-receiver', 'Wechat\WechatController@msgReceiver');
    Route::post('msg-receiver', 'Wechat\WechatController@msgReceiver');
});
Route::middleware('ip_login')->prefix('hi-project')->group(function () {
    Route::get('ip-login', 'API\APIController@ipLogin');
    Route::post('ip-login', 'API\APIController@ipLogin');
    Route::post('add-plan', 'HiProject\PlanController@addPlan');
    Route::post('delete-plan', 'HiProject\PlanController@deletePlan');
    Route::post('delete-task', 'HiProject\TaskController@deleteTask');
    Route::post('update-plan-level', 'HiProject\PlanController@updatePlanLevel');
    Route::post('update-task-level', 'HiProject\TaskController@updateTaskLevel');
    Route::post('plan-list', 'HiProject\PlanController@planList');
    Route::post('task-list', 'HiProject\TaskController@taskList');
    Route::post('add-task', 'HiProject\TaskController@addTask');
    Route::post('update-task', 'HiProject\TaskController@updateTask');
    Route::post('task-details', 'HiProject\TaskController@taskDetails');
    Route::post('task-status', 'HiProject\TaskController@taskStatus');
});