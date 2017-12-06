<?php

namespace PermissionsHandler;

use PermissionsHandler\Roles;
use PermissionsHandler\Permissions;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Illuminate\Foundation\Application as LaravelApplication;

class PermissionsHandlerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();

        // register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \PermissionsHandler\Commands\AddCommand::class,
                \PermissionsHandler\Commands\AssignCommand::class,
                \PermissionsHandler\Commands\SeederCommand::class,
                \PermissionsHandler\Commands\ClearCache::class,
            ]);
        }
        
        // Gate integration
        Gate::before(function (Authenticatable $user, string $ability) {
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability);
            }
            return true;
        });

        // Register blade directives
        Blade::if('permission', function ($permissions, $requireAll = false) {
            return with(new Permissions($permissions, $requireAll))->check();
        });

        Blade::if('role', function ($roles, $requireAll = false) {
            return with(new Roles($roles, $requireAll))->check();
        });
    }

    /**
     * Register the application services.use PermissionsHandler\Commands;.
     
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/Config/permissionsHandler.php', 'permissionsHandler');

        $this->app->bind('permissionsHandler', function () {
            return new PermissionsHandler();
        });

        // register annotation
        AnnotationRegistry::registerFile(__DIR__.'/Annotations/Permissions.php');
        AnnotationRegistry::registerFile(__DIR__.'/Annotations/Roles.php');
        AnnotationRegistry::registerFile(__DIR__.'/Annotations/Owns.php');
    }

    protected function setupConfig()
    {
        $configPath = app()->basePath().'/config/permissionsHandler.php';
        $migrationsPath = app()->basePath().'/database/migrations/'.date('Y_m_d_His').'_create_user_permissions_migrations.php';

        $this->publishes([
            __DIR__.'/Migrations/migrations.php' => $migrationsPath,
        ], 'PermissionsHandler');

        $this->publishes([
            __DIR__.'/Config/permissionsHandler.php' => $configPath,
        ], 'PermissionsHandler');
    }
}
