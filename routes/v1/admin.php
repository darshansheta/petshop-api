<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->get('/user', function (Request $request) {
    return $request->user();
});
