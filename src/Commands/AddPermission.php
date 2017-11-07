<?php

namespace PermissionsHandler\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Models\Permission;

class AddPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:add-perm {permission} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new permission to the system database';


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
        $permission = $this->argument('permission');
        $role = $this->argument('role');

        $permissionModel = Permission::firstOrCreate(['name' =>  $permission]);
        
        $info = "";
        if(isset($role)){
            $roleModel = Role::firstOrCrate(['name' => $role]);
            $rolePermission = Permission::whereHas('roles', function($query) use ($roleModel){
                return $query->where(DB::raw('roles.id'), $roleModel->id);
            });
            if(!$rolePermission){
                $role->permissions()->attach($rolePermission);
            }
        }


    }
}

?>