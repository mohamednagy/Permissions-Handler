<?php

namespace PermissionsHandler;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Illuminate\Support\ServiceProvider;
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
                \PermissionsHandler\Commands\ClearAnnotationsCache::class,
            ]);
        }
    }

    /**
     * Register the application services.use PermissionsHandler\Commands;

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
        AnnotationRegistry::registerFile(__DIR__ . '/Annotations/Permissions.php');
        AnnotationRegistry::registerFile(__DIR__ . '/Annotations/Roles.php');
        AnnotationRegistry::registerFile(__DIR__ . '/Annotations/Owns.php');
    }

    protected function setupConfig()
    {

        if ($this->app instanceof LaravelApplication) {
            require_once __DIR__ . '/Blade/Directives.php';
        }

        $configPath = app()->basePath() . '/config/permissionsHandler.php';
        $migrationsPath = app()->basePath() . '/database/migrations/'.date('Y_m_d_His') . '_create_user_permissions_migrations.php';

        $this->publishes([
            __DIR__ . '/Migrations/migrations.php' => $migrationsPath,
        ],'PermissionsHandler');

        $this->publishes([
            __DIR__ . '/Config/permissionsHandler.php' => $configPath
        ],'PermissionsHandler');
    }
}
