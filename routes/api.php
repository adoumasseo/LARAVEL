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
    Route::put('admin/users/{user}', [UserController::class, 'update']);
    Route::delete('admin/users/{user}', [UserController::class, 'destroy']);
});
Route::middleware('auth:sanctum')->group(function () {
    // authentification
    Route::post('logout', [UserController::class, 'logout']);
    //user profile actionscuser/boards/{board}/tasks
    Route::put('user/profile/{user}', [UserController::class, 'update_self']);
    // cruds boards
    Route::get('user/boards', [BoardController::class, 'index']);
    Route::get('user/boards/{board}/tasks', [BoardController::class, 'show']);
    Route::post('user/boards', [BoardController::class, 'create']);
    Route::put('user/boards/{board}', [BoardController::class, 'update']);
    Route::delete('user/boards/{board}', [BoardController::class, 'destroy']);
    // cruds for tasks endpoint
    Route::get('user/tasks', [TaskController::class, 'index']);
    Route::get('user/tasks/{task}', [TaskController::class, 'show']);
    Route::post('user/tasks', [TaskController::class, 'create']);
    Route::put('user/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('user/tasks/{task}', [TaskController::class, 'destroy']);
});