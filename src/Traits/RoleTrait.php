<?php

namespace PermissionsHandler\Traits;

use PermissionsHandler\Seeder\Seeder;
use Illuminate\Support\Facades\Cache;
use PermissionsHandler\Models\Permission;

trait RoleTrait
{
    /**
     * Delete role.
     *
     * @param array $options
     * @return void
     */
    public function delete()
    {
        $users = $this->users;
        $this->clearRelatedCache();
        parent::delete();
    }

    public function hasPermission($permissions, $requireAll = false)
    {
        if (! is_array($permissions)) {
            $permissions = [$permissions];
        }
        $permissions = collect($permissions)
                        ->flatten()
                        ->map(function($permission){
                            if (is_string($permission)) {
                                $permission = Permission::getByName($permission);
                            }
                            return $permission;
                        })
                        ->pluck('name', 'id')->toArray();
        $cachedPermissions = $this->cachedPermissions()->pluck('name', 'id')->toArray();
        $result = array_intersect($permissions, $cachedPermissions);
        if ($requireAll) {
            return count($result) == count($permissions);
        }

        return count($result) > 0;
    }

    /**
     * Assign many permission to a role.
     *
     * @param Illuminate\Database\Eloquent\Collection|Model|array $permissions
     * @return void
     */
    public function assignPermission(...$permissions)
    {
        $permissions = collect($permissions)
                        ->flatten()
                        ->map(function($permission){
                            if(is_string($permission)){
                                $permission = Permission::getByName($permission);
                            }
                            return $permission;
                        });
                        
        $this->permissions()->saveMany($permissions);

        if (config('permissionsHandler.seeder') == true) {
            Seeder::assignPermissionsToRole($this, $permissions);
        }

        $this->clearRelatedCache();
    }

    /**
     * Remove all permission from a role.
     *
     * @return void
     */
    public function revokeAllPermissions()
    {
        $this->permissions()->sync([]);

        if (config('permissionsHandler.seeder') == true) {
            Seeder::revokeAllRolePermissions($this);
        }

        $this->clearRelatedCache();
    }

    /**
     * Unassign many permission from a role.
     *
     * @param Illuminate\Database\Eloquent\Collection|Model|array $permissions
     * @return void
     */
    public function revokePermission(...$permissions)
    {
        $permissions = collect($permissions)
                        ->flatten()
                        ->map(function($permission){
                            if (is_string($permission)) {
                                $permission = Permission::getByName($permission);
                            }
                            return $permission;
                        })
                        ->pluck('name', 'id')->toArray();

        $cachedPermissions = $this->cachedPermissions()->pluck('name', 'id')->toArray();
        foreach ($permissions as $id => $permission) {
            if (in_array($permission, $cachedPermissions)) {
                unset($cachedPermissions[$id]);
            }
        }

        $this->permissions()->sync($cachedPermissions);

        if (config('permissionsHandler.seeder') == true) {
            Seeder::revokeRolePermissions($this, $permissions);
        }

        $this->clearRelatedCache();
    }


    /**
     * Get the cached permissions for specific role
     *
     * @return Collection
     */
    public function cachedPermissions()
    {
        return Cache::remember(
            $this->getCachePrefix(),
            config('permissionsHandler.cacheExpiration'),
            function() {
                return $this->permissions;
            }
        );
    }

    /**
     * Get role related users.
     * 
     * @return array
     */
    public function getRelatedUsers()
    {
        $relatedUsers = \DB::table(config('permissionsHandler.tables.role_user'))
                    ->select('role_id', 'model_id', 'model_type')
                    ->where('role_id', $this->id)
                    ->get();

        $users = collect();
        foreach ($relatedUsers as $user) {
            $users->push(($user->model_type)::find($user->model_id));
        }

        return $users;
    }

    /**
     * Clear all caches related to this role.
     *
     * @return void
     */
    public function clearRelatedCache()
    {
        Cache::forget($this->getCachePrefix());
        $users = $this->getRelatedUsers();
        foreach ($users as $user) {
            $user->clearCachedRoles();
            $user->clearCachedPermissions();
        }
    }


    public function getCachePrefix()
    {
        return 'permissionsHandler.roles.'.$this->id.'permissions';
    }
}
