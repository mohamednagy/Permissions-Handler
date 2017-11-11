<?php

namespace PermissionsHandler\Traits;

use Illuminate\Support\Facades\DB;
use PermissionsHandler\Models\Permission;
use PermissionsHandler\Models\Role;

trait CrudTrait
{
    public function user($id = null)
    {
        $user = config('permissionsHandler.user');
        if ($id) {
            return $user::find($id);
        }

        return new $user();
    }

    public function addPermission($permission)
    {
        return Permission::firstOrCreate(['name' => $permission]);
    }

    public function addRole($role)
    {
        return Role::firstOrCreate(['name' => $role]);
    }

    public function assignPermissionToRole($permission, $role)
    {
        $permissions = $role->permissions->pluck('id')->toArray();
        if (in_array($permission->id, $permissions)) {
            return false;
        }
        $role->permissions()->attach($permission->id);
        //clear related users caches
        $users = with($this->user())::whereHas('roles', function ($query) use ($role) {
            return $query->where(DB::raw('roles.id'), $role->id);
        })->chunk(1000, function ($users) {
            foreach ($users as $user) {
                $user->clearCachedRoles();
                $user->clearCachedPermissions();
            }
        });

        return true;
    }

    public function assignRoleToUser($role, $user)
    {
        $userRoles = $user->cachedRoles();
        if (in_array($role->id, array_keys($userRoles))) {
            return false;
        }

        $user->roles()->attach($role->id);
        $user->clearCachedRoles();

        return true;
    }
}
