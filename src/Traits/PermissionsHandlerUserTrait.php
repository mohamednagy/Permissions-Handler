<?php

namespace PermissionsHandler\Traits;

use PermissionsHandler;
use PermissionsHandler\Models\Role;

trait PermissionsHandlerUserTrait {


    /**
     * many to many relation with PermissionsHandler\Models\Role
     */
    public function roles(){
        return $this->belongsToMany(\PermissionsHandler\Models\Role::class);
    }


    /**
     * if the user has a permission to do action
     *
     * @param array|string $permissions
     * @return boolean
     */
    public function canDo($permissions){
        return PermissionsHandler::hasPermissions($permissions);
    }

    /**
     * if a user has a specific permissions
     *
     * @param array|string $permissions
     * @return boolean
     */
    public function hasPermission($permissions){
        return PermissionsHandler::hasPermissions($permissions);
    }

    /**
     * if a user has a role
     *
     * @param string $role
     * @return boolean
     */
    public function hasRole($role){
        $role = Role::whereName($role)->first();
        if(!$role){
            return false;
        }
        return in_array($role->id, PermissionsHandler::getUserRoles());
    }


}









?>