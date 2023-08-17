<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Admin\AuthController;

Route::prefix('admin')->group( function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:jwt')->group(function () {
        Route::delete('logout', [AuthController::class, 'logout']);
    });
});
