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
    public function getUserRoles($user = null)
    {
        if(!$user){
            $user = $this->user;
        }
        return  Cache::remember(
                    'user_'.$user->id.'_roles',
                    $this->config['cacheExpiration'] ,
                    function() use ($user){
                            return  $user->roles->pluck('id')->toArray();
                    }
                );
    }


    /**
     * get the permission for specific roles
     *
     * @param array $roles
     * @return void
     */
    public function getRolePermissions($roles)
    {
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

    /**
     * clear all cached permissions
     *
     * @return void
     */
    public function clearCache()
    {
        // get users which have roles
        $user = PermissionsHandler::getUser();
        $usersHaveRoles = $user->whereHas('roles')->chunk(1000, function($users){
            foreach($users as $user){
                // clear cached roles
                $this->clearUserCache($user);
            }
        });
    }

    /**
     * clear cached permissions for specific user
     *
     * @param Illuminate\Database\Eloquent\Model $user
     * @return void
     */
    public function clearUserCache($user)
    {
        Cache::forget('user_'.$user->id.'_roles');
        Cache::forget('user_'.$user->id.'_permissions');
    }

    /**
     * clear cached permissions for specifc roles and related users
     *
     * @param Illuminate\Database\Eloquent\Model $role
     * @return void
     */
    public function clearRoleCache($role)
    {
        $user = PermissionsHandler::getUser();
        $usersHaveRole = $user->whereHas('roles', function($query) use ($role){
            return $query->where(DB::raw('roles.id'), $role->id);
        })->chunk(1000, function($users){
            foreach($users as $user){
                $this->clearUserCache($user);
            }
        });
    }

    /**
     * clear all cached annotations
     *
     * @return void
     */
    public function clearAnnotationsCache()
    {
        PermissionsHandler::clearAnnotationsCache();
    }


}









?>