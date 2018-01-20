<?php

namespace PermissionsHandler\Traits;

use PermissionsHandler;
use PermissionsHandler\Models\Role;
use Illuminate\Support\Facades\Cache;
use Doctrine\Common\Inflector\Inflector;
use PermissionsHandler\Models\Permission;
use Illuminate\Database\Eloquent\Builder;

trait UserTrait
{

    public static function bootUserTrait()
    {
        Builder::macro('withRole', function($role) {
            return Builder::whereHas('roles', function($q) use ($role) {
                return $q->where('name', $role);
            });
        });
    }
    /**
     * many to many relation with PermissionsHandler\Models\Role.
     */
    public function roles()
    {
        $rolesForeignKeyName = Inflector::singularize(config('permissionsHandler.tables.roles')).'_id';

        return $this->morphToMany(
            Role::class,
            'model',
            config('permissionsHandler.tables.role_user'),
            'model_id',
            $rolesForeignKeyName
        );
    }

    /**
     * delete user.
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
                        $rolesForeignKeyName = Inflector::singularize(config('permissionsHandler.tables.roles')).'_id';

                        return $query->whereIn($rolesForeignKeyName, array_keys($roles));
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
        if (! is_array($roles)) {
            $roles = [$roles];
        }
        $roles = collect($roles)
                    ->flatten()
                    ->map(function ($role) {
                        if (is_string($role)) {
                            $role = Role::getByName($role);
                        }

                        return $role;
                    })
                    ->pluck('name', 'id')->toArray();

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
        if (! is_array($permissions)) {
            $permissions = [$permissions];
        }
        $permissions = collect($permissions)
                    ->flatten()
                    ->map(function ($permission) {
                        if (is_string($permission)) {
                            $permission = Permission::getByName($permission);
                        }

                        return $permission;
                    })
                    ->pluck('name', 'id')->toArray();

        $cachedPermissions = $this->cachedPermissions();
        $result = array_intersect($permissions, $cachedPermissions);
        if ($requireAll) {
            return count($result) == count($permissions);
        }

        return count($result) > 0;
    }

    /**
     * assign role to user.
     *
     * @param Illuminate\Database\Eloquent\Model $role
     * @return void
     */
    public function assignRole(...$roles)
    {
        $roles = collect($roles)
                ->flatten()
                ->map(function ($role) {
                    if (is_string($role)) {
                        $role = Role::where('name', $role)->first();
                    }

                    return $role;
                })
                ->all();
        $this->roles()->saveMany($roles);
        $this->clearCachedRoles();
    }

    /**
     * remove a role from a user.
     *
     * @param \PermissionsHandler\Models\Role|array $role
     * @return void
     */
    public function revokeRole(...$roles)
    {
        $roles = collect($roles)
                    ->flatten()
                    ->map(function ($role) {
                        if (is_string($role)) {
                            $role = Role::getByName($role);
                        }

                        return $role;
                    })
                    ->pluck('name', 'id');

        $userRoles = $this->cachedRoles();
        foreach ($roles as $id => $role) {
            if (in_array($role, $userRoles)) {
                unset($userRoles[$id]);
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
    public function revokeAllRoles()
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
        return 'permissionsHandler'.$this->getTable().'_'.$this->id;
    }

    /**
     * If the user owns a specific resource.
     * 
     * @param string $realtion
     * @param string $parameter
     * @param string $key
     * 
     * @return bool
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

    public function scopeWithRole($query, $role)
    {
        return $query->whereHas('roles', function($q) use ($role) {
            return $q->where('name', $role);
        });
    }
}
