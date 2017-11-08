<?php

namespace PermissionsHandler\Traits;

use Illuminate\Support\Facades\DB;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Models\Permission;

trait PermissionsHandlerTrait {

    /**
     * assign a permission to role
     *
     * @param string $permissionName
     * @param string $roleName
     * @return bool
     */
    public function assignPermissionToRole($permissionName, $roleName)
    {
        DB::beginTransaction();
        try{
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role = Role::firstOrCreate(['name' => $roleName]);
            $hasPermission = Permission::whereHas('roles', function($query) use ($role){
                return $query->where(DB::raw('roles.id'), $role->id);
            })->where('name', $permission)->first();
            if(!$hasPermission){
                $role->permissions()->attach($permission->id);
            }
            // clear related role permissions
            $this->clearRoleCache($role);
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }


    /**
     * Assign specific role to user
     *
     * @param Illuminate\Database\Eloquent\Model $user $user
     * @param string $roleName
     * @return void
     */
    public function assignUserToRole($user, $roleName)
    {
        DB::beginTransaction();
        try{
            $role = Role::firstOrCreate(['name' => $roleName]);
            $userRoles = $this->getUserRoles($user);
            if(!in_array($role->id, $userRoles)){
                $user->roles()->attach($role->id);
            }
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }


}

?>