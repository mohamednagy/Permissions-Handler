<?php

namespace PermissionsHandler\Traits;

use PermissionsHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use PermissionsHandler\Models\Permission;

trait PermissionsHandlerCacheTrait {

    /**
     * get all user roles
     *
     * @return array
     */
    public function getUserRoles(){
        return  Cache::remember(
                    'user_'.$this->user->id.'_roles',
                    $this->config['cacheExpiration'] ,
                    function() {
                            return  $this->user->roles->pluck('id')->toArray();
                    }
                );
    }


    public function getRolePermissions($roles){
        return  Cache::remember(
                    'user_'.$this->user->id.'_permissions',
                    $this->config['cacheExpiration'],
                    function() use ($roles) {
                        return Permission::whereHas('roles', function ($query) use ($roles) {
                                    return $query->whereIn(DB::raw('roles.id'), $roles);
                            })->pluck('name')->toArray();
                    }
                );
    }


}









?>