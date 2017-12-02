<?php

namespace PermissionsHandler\Commands;

use Illuminate\Console\Command;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Seeder\Seeder;
use PermissionsHandler\Models\Permission;

class SeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:seed {--permissions=} {--roles=} {--role-permissions} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the saved roles and permissions to the database';

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
        $permissions = $this->option('permissions');
        $roles = $this->option('roles');
        $rolePermissions = $this->option('role-permissions');
        $all = $this->option('all');

        // seed permissions
        if (! $permissions || $all || $rolePermissions) {
            $permissions = Seeder::getFileContent('permissions.json');
            foreach ($permissions as $permission) {
                $result = Permission::whereName($permission['name'])->first();
                if (! $result) {
                    Permission::create($permission);
                }
            }
            $this->info('permissions has been seeded');
        }

        // seed roles
        if ($roles || $rolePermissions || $all) {
            $roles = Seeder::getFileContent('roles.json');
            foreach ($roles as $role) {
                $result = Role::whereName($role['name'])->first();
                if (! $result) {
                    Role::create($role);
                }
            }
            $this->info('roles has been seeded');
        }

        // assign permissions to roles
        if ($rolePermissions || $all) {
            $rolePermissions = Seeder::getFileContent('role-permissions.json');
            foreach ($rolePermissions as $roleName => $permissionsName) {
                $role = Role::whereName($roleName)->first();
                foreach ($permissionsName as $permission) {
                    if (! $role->hasPermission($permission)) {
                        $permission = Permission::whereName($permission)->first();
                        $role->assignPermission($permission);
                    }
                }
            }
            $this->info('Permissions have been assigned to roles');
        }
    }
}
