<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'users'], function () {

        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);

    });

    Route::group(['prefix' => 'cars'], function () {

        Route::get('/', [CarController::class, 'index']);
        Route::get('/{id}', [CarController::class, 'show']);
        Route::post('/', [CarController::class, 'store']);
        Route::put('/{id}', [CarController::class, 'update']);
        Route::delete('/{id}', [CarController::class, 'destroy']);

    });

});