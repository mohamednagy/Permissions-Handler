<?php

namespace PermissionsHandler;

use Illuminate\Support\ServiceProvider;

class PermissionsHandlerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Migrations' => base_path('database/migrations/'),
            __DIR__.'/Config' => base_path('config'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'PermissionsHandler\PermissionsHandlerInterface',
            'PermissionsHandler\PermissionsHandler'
        );
    }
}
