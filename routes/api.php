<?php

use App\Http\Controllers\BoardController;
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
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::middleware(['auth:sanctum', 'admin'])->group(function(){
    Route::get('admin/dashboard', [UserController::class, 'index']);
    Route::put('admin/update-user/{user}', [UserController::class, 'update']);
    Route::delete('admin/delete-user/{user}', [UserController::class, 'destroy']);
});
Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [UserController::class, 'logout']);
    Route::put('user/update-profile/{user}', [UserController::class, 'update_self']);
    Route::get('user/boards', [BoardController::class, 'index']);
    Route::post('user/create-board', [BoardController::class, 'create']);
    Route::put('user/update-board/{board}', [BoardController::class, 'update']);
    Route::delete('user/delete-board/{board}', [BoardController::class, 'destroy']);
});