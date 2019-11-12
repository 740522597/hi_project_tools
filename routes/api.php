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
Route::prefix('hi-project')->group(function () {
    Route::post('login', 'AuthController@login');
    Route::get('task-file/{one?}/{two?}/{three?}/{four?}/{five?}/{six?}/{seven?}/{eight?}/{nine?}', function () {
        \App\Http\Controllers\ImageRoute::imageStorageRoute();
    });
});
Route::middleware('auth:api')->prefix('hi-project')->group(function () {
//    Route::get('ip-login', 'API\APIController@ipLogin');
    // TaskController
    Route::post('add-task', 'HiProject\TaskController@addTask');
    Route::post('delete-task', 'HiProject\TaskController@deleteTask');
    Route::post('update-task', 'HiProject\TaskController@updateTask');
    Route::post('task-list', 'HiProject\TaskController@taskList');
    Route::post('task-details', 'HiProject\TaskController@taskDetails');
    Route::post('load-goal-tasks', 'HiProject\TaskController@loadGoalTasks');
    Route::post('task-status', 'HiProject\TaskController@taskStatus');
    Route::post('update-task-level', 'HiProject\TaskController@updateTaskLevel');
    Route::post('load-comments', 'HiProject\TaskController@loadComments');
    Route::post('delete-comment', 'HiProject\TaskController@deleteComment');
    Route::post('add-task-comment', 'HiProject\TaskController@addTaskComment');
    Route::post('get-files', 'HiProject\TaskController@getFiles');
    Route::post('delete-file', 'HiProject\TaskController@deleteFile');
    Route::post('upload-file', 'HiProject\TaskController@uploadFile');
    Route::post('add-sub-task', 'HiProject\TaskController@addSubTask');
    Route::post('sub-tasks-list', 'HiProject\TaskController@subTaskList');
    Route::post('delete-sub-task', 'HiProject\TaskController@deleteSubTask');
    Route::post('finish-sub-task', 'HiProject\TaskController@finishSubTask');

    // APIController
    Route::post('ip-login', 'API\APIController@ipLogin');

    // PlanController
    Route::post('add-plan', 'HiProject\PlanController@addPlan');
    Route::post('delete-plan', 'HiProject\PlanController@deletePlan');
    Route::post('archive-plan', 'HiProject\PlanController@archivePlan');
    Route::post('update-plan-level', 'HiProject\PlanController@updatePlanLevel');
    Route::post('plan-list', 'HiProject\PlanController@planList');
    Route::post('get-plan', 'HiProject\PlanController@getPlan');

    // ArchivedPlanController
    Route::post('archived-plan-list', 'HiProject\ArchivedPlanController@planList');
    Route::post('archived-task-list', 'HiProject\ArchivedTaskController@taskList');
    Route::post('archived-task-details', 'HiProject\ArchivedTaskController@taskDetails');

    //ProjectController
    Route::post('load-projects', 'HiProject\ProjectController@loadProjects');

    //ArchivedTaskController
    Route::post('archived-load-comments', 'HiProject\ArchivedTaskController@loadComments');
    Route::post('archived-get-files', 'HiProject\ArchivedTaskController@getFiles');

    //AuthController
    Route::post('logout', 'AuthController@logout');
    Route::post('me', 'AuthController@me');

    //Notify
    Route::post('notify', 'API\NotifyController@notify');
});