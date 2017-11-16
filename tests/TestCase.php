<?php

namespace PermissionsHandler\Tests;

use Illuminate\Database\Schema\Blueprint;
use Monolog\Handler\TestHandler;
use Orchestra\Testbench\TestCase as Orchestra;
use PermissionsHandler\Models\Permission;
use PermissionsHandler\Models\Role;
use PermissionsHandler\PermissionsHandlerServiceProvider;
use PermissionsHandler\Tests\Models\User;

abstract class TestCase extends Orchestra
{
    /** @var \PermissionsHandler\Tests\Models\User */
    protected $testUser;
    /** @var \PermissionsHandler\Models\Role */
    protected $testUserRole;
    /** @var \PermissionsHandler\Models\Role */
    protected $testAdminRole;
    /** @var \PermissionsHandler\Models\Permission */
    protected $testUserPermission;
    /** @var \PermissionsHandler\Models\Permission*/
    protected $testAdminPermission;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDatabase($this->app);
        $this->testUser = User::find(1);
        $this->testAdmin = User::find(2);

        $this->testUserRole = app(Role::class)->find(1);
        $this->testAdminRole = app(Role::class)->find(3);

        $this->testUserPermission = app(Permission::class)->find(1);
        $this->testAdminPermission = app(Permission::class)->find(3);

        $this->clearLogTestHandler();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            PermissionsHandlerServiceProvider::class,
        ];
    }
    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'permissions'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', 'root'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        $app['config']->set('permissionsHandler.user', User::class);
        // Use test User model for users provider
        $app['config']->set('auth.providers.users.model', User::class);
        $app['log']->getMonolog()->pushHandler(new TestHandler());
    }
    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        if (!$app['db']->connection()->getSchemaBuilder()->hasTable('users'))
        {
            $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('email');
                $table->softDeletes();
            });
        }

        include_once __DIR__.'/../src/Migrations/migrations.php';

        (new \CreateUserPermissionsMigrations())->up();

        User::firstOrCreate(['email' => 'test@user.com']);
        User::firstOrCreate(['email' => 'admin@user.com']);

        $app[Role::class]->firstOrCreate(['name' => 'testRole']);
        $app[Role::class]->firstOrCreate(['name' => 'testRole2']);
        $app[Role::class]->firstOrCreate(['name' => 'testAdminRole']);

        $app[Permission::class]->firstOrCreate(['name' => 'user-permission']);
        $app[Permission::class]->firstOrCreate(['name' => 'edit-news']);
        $app[Permission::class]->firstOrCreate(['name' => 'admin-permission']);
    }

    /**
     * Reload the permissions.
     */
    protected function reloadPermissions()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
    /**
     * Refresh the testuser.
     */
    public function refreshTestUser()
    {
        $this->testUser = $this->testUser->fresh();
    }
    /**
     * Refresh the testAdmin.
     */
    public function refreshTestAdmin()
    {
        $this->testAdmin = $this->testAdmin->fresh();
    }
    protected function clearLogTestHandler()
    {
        collect($this->app['log']->getMonolog()->getHandlers())->filter(function ($handler) {
            return $handler instanceof TestHandler;
        })->first(function (TestHandler $handler) {
            $handler->clear();
        });
    }
    protected function assertNotLogged($message, $level)
    {
        $this->assertFalse($this->hasLog($message, $level), "Found `{$message}` in the logs.");
    }
    protected function assertLogged($message, $level)
    {
        $this->assertTrue($this->hasLog($message, $level), "Couldn't find `{$message}` in the logs.");
    }
    /**
     * @param $message
     * @param $level
     *
     * @return bool
     */
    protected function hasLog($message, $level)
    {
        return collect($this->app['log']->getMonolog()->getHandlers())->filter(function ($handler) use ($message, $level) {
                return $handler instanceof TestHandler
                    && $handler->hasRecordThatContains($message, $level);
            })->count() > 0;
    }

    protected function getPackageAliases($app)
    {
        return [
            'PermissionsHandler' => \PermissionsHandler\Facades\PermissionsHandlerFacade::class
        ];
    }
}