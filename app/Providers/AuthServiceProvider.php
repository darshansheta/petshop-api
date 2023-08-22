<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Services\Auth\MyTokenGuard;
use App\Services\Auth\MyJWT;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->app->bind('my-jwt.core', function ($app) {
            return new MyJWT();
        });
        // add custom guard
        Auth::extend('my-jwt', function ($app, $name, array $config) {
            $guard = new MyTokenGuard($app['my-jwt.core'], Auth::createUserProvider($config['provider']), $app->make('request'));

            $app->refresh('request', $guard, 'updateRequest');

            return $guard;
        });
    }
}
