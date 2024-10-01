<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// authentification
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::middleware(['auth:sanctum', 'admin'])->group(function(){
    // admin crud on users 
    Route::get('admin/dashboard', [UserController::class, 'index']);
    Route::put('admin/update-user/{user}', [UserController::class, 'update']);
    Route::delete('admin/delete-user/{user}', [UserController::class, 'destroy']);
});
Route::middleware('auth:sanctum')->group(function () {
    // authentification
    Route::post('logout', [UserController::class, 'logout']);
    //user profile actions
    Route::put('user/update-profile/{user}', [UserController::class, 'update_self']);
    // cruds boards
    Route::get('user/boards', [BoardController::class, 'index']);
    Route::get('user/board-get-tasks/{board}', [BoardController::class, 'show']);
    Route::post('user/create-board', [BoardController::class, 'create']);
    Route::put('user/update-board/{board}', [BoardController::class, 'update']);
    Route::delete('user/delete-board/{board}', [BoardController::class, 'destroy']);
    // cruds for tasks
    Route::get('user/tasks', [TaskController::class, 'index']);
    Route::get('user/show-task/{task}', [TaskController::class, 'show']);
    Route::post('user/create-task', [TaskController::class, 'create']);
    Route::put('user/update-task/{task}', [TaskController::class, 'update']);
    Route::delete('user/delete-task/{task}', [TaskController::class, 'destroy']);

});