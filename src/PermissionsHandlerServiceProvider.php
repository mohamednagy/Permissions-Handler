<?php

namespace PermissionsHandler;

use Illuminate\Support\ServiceProvider;
use PermissionsHandler\PermissionsHandler;
use PermissionsHandler\PermissionsHandlerInterface;
use Doctrine\Common\Annotations\AnnotationRegistry; 

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
        $this->app->singleton(PermissionsHandlerInterface::class, function () {
            $model = config('permissionsHandler.user');
            $repository = new PermissionsHandler(new $model);
            return $repository;
        });
        // register annotation
        AnnotationRegistry::registerFile(__DIR__.'/Permissions.php');
    }
}
