<?php

namespace Darshan\DailyExchanger;

use Illuminate\Support\ServiceProvider;

class ExchangeServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
    }

    public function register()
    {
        $this->app->singleton(MyPackage::class, function () {
            return new DailyExchanger();
        });
        $this->app->alias(DailyExchanger::class, 'daily-exchanger');
    }
}
