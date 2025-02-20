<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/register', UserController::class . '@store');
Route::post('/login', AuthController::class . '@login');
Route::get('/verify', CertificateController::class . '@verify');

Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', UserController::class . '@index');
    Route::get('/{id}', UserController::class . '@show');
    Route::put('/{id}', UserController::class . '@update');
    Route::delete('/{id}', UserController::class . '@destroy');
});

Route::prefix('certificates')->middleware('auth:sanctum')->group(function () {
    Route::get('/', CertificateController::class . '@index');
    Route::post('/', CertificateController::class . '@store');
    Route::get('/{id}', CertificateController::class . '@show')->where('id', '[0-9]+');
    Route::put('/{id}', CertificateController::class . '@update')->where('id', '[0-9]+');
    Route::delete('/{id}', CertificateController::class . '@destroy')->where('id', '[0-9]+');
});
