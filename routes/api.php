<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;

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

 Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post("create-user", [UserController::class, 'createUser']);

Route::post("user-login", [UserController::class, 'userLogin']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::post("create-task", [TaskController::class, 'createTask']);

    Route::get("tasks", [TaskController::class, 'tasks']);

    Route::put("task/{task_id}", [TaskController::class, 'updateTask']);

    Route::get("task/{task_id}", [TaskController::class, 'task']);

    Route::delete("task/{task_id}", [TaskController::class, 'deleteTask']);

});
