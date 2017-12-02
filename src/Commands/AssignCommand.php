<?php

namespace PermissionsHandler\Commands;

use PermissionsHandler;
use Illuminate\Console\Command;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Models\Permission;

class AssignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:assign {--role=} {--user-id=} {--permission=} {--guard=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign permission to role or user to role';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $roleName = $this->option('role');
        $permissionName = $this->option('permission');
        $guard = $this->option('guard');

        if (! $userId && ! $roleName && ! $permissionName) {
            $this->error('missing parameters!');

            return;
        }

        $permission = null;
        if ($permissionName) {
            $permission = Permission::firstOrCreate(['name' =>  $permissionName]);
        }

        $role = null;
        if ($roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
        }

        $user = null;
        if ($userId) {
            $guard = $guard ?: config('auth.defaults.guard');
            $provider = config('auth.guards.'.$guard.'.provider');
            $user = config('auth.providers.'.$provider.'.model');
            $user = $user::find($userId);
        }

        if ($role && $permission) {
            $role->assignPermission($permission);
            $this->info("`$permissionName` has been assigned to `$roleName`");
        }

        if ($user && $role) {
            $user->assignRole($role);
            $this->info("`$roleName` has been assigned to user `$userId`");
        }
    }
}
