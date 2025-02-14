<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/register', UserController::class . '@store');
Route::post('/login', AuthController::class . '@login');

Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', UserController::class . '@index');
    Route::get('/{id}', UserController::class . '@show');
    Route::put('/{id}', UserController::class . '@update');
    Route::delete('/{id}', UserController::class . '@destroy');
});
