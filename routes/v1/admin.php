<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Admin\AuthController;
use App\Http\Controllers\API\V1\Admin\AdminController;
use App\Http\Controllers\API\V1\Admin\UsersController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:jwt')->group(function () {
        Route::delete('logout', [AuthController::class, 'logout'])->name('logout');

        Route::post('create', [AdminController::class, 'store'])->name('create');

        Route::controller(UsersController::class)->name('user.')->group(function () {
            Route::get('/user-listing', 'index')->name('listing');
            Route::put('/user-edit/{user}', 'update')->name('edit');
            Route::delete('/user-delete/{user}', 'delete')->name('delete');
        });
    });
});
