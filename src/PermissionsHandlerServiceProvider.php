<?php

namespace PermissionsHandler;

use Illuminate\Support\Facades\Blade;
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
        // register blade directive
        Blade::if('canDo', function ($permissions) {
            return \PermissionsHandler::hasPermissions($permissions);
        });
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
        $this->app->singleton('permissionsHandler', function () {
            $permissionsHandler = new PermissionsHandler(auth()->user());
            return $permissionsHandler;
        });
        // register annotation
        AnnotationRegistry::registerFile(__DIR__.'/Permissions.php');
    }
}
