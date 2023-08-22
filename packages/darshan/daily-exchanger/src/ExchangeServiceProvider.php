<?php

namespace Darshan\DailyExchanger;

use Illuminate\Support\ServiceProvider;

class ExchangeServiceProvider extends ServiceProvider
{

	/**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
    }
    /**
     * Register the application services.
     *
     *@return void
     */
    public function register()
    {
        $this->app->singleton(MyPackage::class, function () {
            return new DailyExchanger();
        });        
		$this->app->alias(DailyExchanger::class, 'daily-exchanger');
    }
}