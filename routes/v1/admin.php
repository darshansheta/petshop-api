<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Admin\AuthController;
use App\Http\Controllers\API\V1\Admin\AdminController;

Route::prefix('admin')->group( function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:jwt')->group(function () {
        Route::delete('logout', [AuthController::class, 'logout']);

        Route::post('create', [AdminController::class, 'store']);
    });
});
