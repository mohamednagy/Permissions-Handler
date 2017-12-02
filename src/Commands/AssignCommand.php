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
    protected $signature = 'permissions:assign {--role=} {--user-id=} {--permission=}';

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
            $user = PermissionsHandler::user($userId);
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
