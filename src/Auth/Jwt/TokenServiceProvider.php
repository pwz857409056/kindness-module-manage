<?php

namespace Kindness\ModuleManage\Auth\Jwt;

use Illuminate\Support\ServiceProvider;

class TokenServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('token', function ($app) {
            return new Token($app);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
