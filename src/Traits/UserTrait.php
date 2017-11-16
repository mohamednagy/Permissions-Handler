<?php

namespace PermissionsHandler\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PermissionsHandler;
use PermissionsHandler\Models\Permission;
use PermissionsHandler\Models\Role;

trait UserTrait
{
    /**
     * many to many relation with PermissionsHandler\Models\Role.
     */
    public function roles()
    {
        return $this->belongsToMany(\PermissionsHandler\Models\Role::class);
    }

    /**
     * delete user
     *
     * @param array $options
     * @return void
     */
    public function delete()
    {
        $this->clearCachedRoles();
        parent::delete();
    }

    /**
     * Get user cached roles.
     *
     * @return array
     */
    public function cachedRoles()
    {
        return  Cache::remember(
            $this->getCachePrefix().'_roles',
            config('permissionsHandler.cacheExpiration'),
            function () {
                return  $this->roles->pluck('name', 'id')->toArray();
            }
        );
    }

    /**
     * Get user cached permissions.
     *
     * @return array
     */
    public function cachedPermissions()
    {
        $roles = $this->cachedRoles();

        return  Cache::remember(
            $this->getCachePrefix().'_permissions',
            config('permissionsHandler.cacheExpiration'),
            function () use ($roles) {
                return Permission::whereHas(
                    'roles', function ($query) use ($roles) {
                        return $query->whereIn(DB::raw('roles.id'), array_keys($roles));
                    }
                )->pluck('name', 'id')->toArray();
            }
        );
    }

    /**
     * Check if the user has a role.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return in_array($role, $this->cachedRoles());
        } else {
            return in_array($role->id, $this->cachedRoles());
        }
    }

    /**
     * Check if the user has a permission.
     *
     * @param string $permission
     *
     * @return bool
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->cachedPermissions());
    }

    /**
     * Check if the user has a permission, is alias for hasPermission.
     *
     * @param string $permission
     *
     * @return bool
     */
    public function canDo($permission)
    {
        return in_array($permission, $this->cachedPermissions());
    }

    /**
     * assign role to user
     *
     * @param Illuminate\Database\Eloquent\Model $role
     * @return void
     */
    public function assignRole($role)
    {
        $this->roles()->attach($role->id);
        $this->clearCachedRoles();
    }

    /**
     * remove a role from a user
     *
     * @param \Illuminate\Database\Eloquent\Model $role
     * @return void
     */
    public function unAssignRole($role)
    {
        $this->roles()->detach($role->id);
        $this->clearCachedRoles();
    }

    /**
     * Clear user cached roles.
     *
     * @return void
     */
    public function clearCachedRoles()
    {
        Cache::forget($this->getCachePrefix().'_roles');
    }

    /**
     * Clear user cached permissions.
     *
     * @return void
     */
    public function clearCachedPermissions()
    {
        Cache::forget($this->getCachePrefix().'_permissions');
    }

    /**
     * Get the cache prefix, used for caching keys.
     *
     * @return string
     */
    public function getCachePrefix()
    {
        return $this->getTable().'_'.$this->id;
    }
}
