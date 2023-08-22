<?php

use Illuminate\Support\Facades\Route;
use Darshan\DailyExchanger\Controllers\CurrencyExchangeController;

Route::get('/daily-exchanger/currency-to', [CurrencyExchangeController::class, 'convertCurrency']);
