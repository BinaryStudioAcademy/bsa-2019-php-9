<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Services\Contracts\AuthService::class, \App\Services\AuthService::class);
        $this->app->bind(\App\Repositories\Contracts\UserRepository::class, \App\Repositories\UserRepository::class);
        $this->app->bind(\App\Services\Contracts\PhotoService::class, \App\Services\PhotoService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
