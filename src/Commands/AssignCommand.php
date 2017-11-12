<?php

namespace PermissionsHandler\Commands;

use Illuminate\Console\Command;
use PermissionsHandler;
use PermissionsHandler\Models\Role;

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

        if (!$userId && !$roleName && !$permissionName) {
            $this->error('missing parameters!');

            return;
        }

        $permission = null;
        if ($permissionName) {
            $permission = PermissionsHandler::addPermission($permissionName);
        }

        $role = null;
        if ($roleName) {
            $role = PermissionsHandler::addRole($roleName);
        }

        $user = null;
        if ($userId) {
            $user = PermissionsHandler::user($userId);
        }

        if ($role && $permission) {
            PermissionsHandler::assignPermissionToRole($permission, $role);
            $this->info("`$permissionName` has been assigned to `$roleName`");
        }

        if ($user && $role) {
            PermissionsHandler::assignRoleToUser($user, $role);
            $this->info("`$roleName` has been assigned to user `$userId`");
        }
    }
}
