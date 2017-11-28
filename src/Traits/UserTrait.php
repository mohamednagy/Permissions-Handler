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
     * @param string|array $role
     *
     * @return bool
     */
    public function hasRole($roles, $requireAll = false)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $cachedRoles = $this->cachedRoles();
        $result = array_intersect($roles, $cachedRoles);
        if ($requireAll) {
            return count($result) == count($roles);
        }
        return count($result) > 0;
    }

    /**
     * Check if the user has a permission.
     *
     * @param string|array $permissions
     *
     * @return bool
     */
    public function hasPermission($permissions, $requireAll = false)
    {
        if (!is_array($permissions)){
            $permissions = [$permissions];
        }
        $cachedPermissions = $this->cachedPermissions();
        $result = array_intersect($permissions, $cachedPermissions);
        if ($requireAll) {
            return count($result) == count($permissions);
        }
        return count($result) > 0;
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
        if (!$this->roles->contains('id', $role->id)) {
            $this->roles()->attach($role->id);
            $this->clearCachedRoles();
        }
    }

    /**
     * remove a role from a user
     *
     * @param \PermissionsHandler\Models\Role|array $role
     * @return void
     */
    public function unAssignRole($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        $userRoles = $this->cachedRoles();
        foreach ($roles as $role) {
            if (isset($userRoles[$role->id])) {
                unset($userRoles[$role->id]);
            }
        }
        $this->roles()->sync($userRoles);

        $this->clearCachedRoles();
        $this->clearCachedPermissions();
    }


    /**
     * Remove all assiged roles.
     *
     * @return void
     */
    public function unAssignAllRoles()
    {
        $this->roles()->sync([]);

        $this->clearCachedRoles();
        $this->clearCachedPermissions();
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

    /**
     * If the user owns a specific resource 
     * 
     * @param string $realtion
     * @param string $parameter
     * @param string $key
     * 
     * @return boolean
     */
    public function owns($relation, $parameter, $key = null)
    {
        $request = app(\Illuminate\Http\Request::class);
        if ($key == null) {
            $key = $parameter;
        }
        
        $result = $this->{$relation}->contains($key, $request->{$parameter});
        
        return $result;
    }
}
