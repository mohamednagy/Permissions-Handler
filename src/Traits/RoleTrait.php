<?php

namespace PermissionsHandler\Traits;

use PermissionsHandler\Seeder\Seeder;

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

    public function hasPermission($permission)
    {
        $hasPermission = false;
        if (is_string($permission)) {
            $hasPermission = $this->permissions->contains('name', $permission);
        } elseif (is_object($permission)) {
            $hasPermission = $this->permissions->contains('id', $permission->id);
        }

        return $hasPermission;
    }

    /**
     * Assign many permission to a role.
     *
     * @param Illuminate\Database\Eloquent\Collection|Model|array $permissions
     * @return void
     */
    public function assignPermission($permissions)
    {
        if (! is_array($permissions)) {
            $permissions = [$permissions];
        }
        $rolePermissions = $this->permissions->pluck('id')->toArray();
        foreach ($permissions as $permission) {
            if (in_array($permission->id, $rolePermissions)) {
                continue;
            }
            $rolePermissions[] = $permission->id;
        }
        $this->permissions()->sync($rolePermissions);

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
    public function unAssignAllPermissions()
    {
        $this->permissions()->sync([]);

        if (config('permissionsHandler.seeder') == true) {
            Seeder::unAssignAllRolePermissions($this);
        }

        $this->clearRelatedCache();
    }

    /**
     * Unassign many permission from a role.
     *
     * @param Illuminate\Database\Eloquent\Collection|Model|array $permissions
     * @return void
     */
    public function unAssignPermission($permissions)
    {
        if (! is_array($permissions)) {
            $permissions = [$permissions];
        }
        $rolePermissions = $this->permissions->pluck('id')->toArray();
        foreach ($permissions as $permission) {
            if (($key = array_search($permission->id, $rolePermissions)) !== false) {
                unset($rolePermissions[$key]);
            }
        }
        $this->permissions()->sync($rolePermissions);

        if (config('permissionsHandler.seeder') == true) {
            Seeder::unAssignRolePermissions($this, $permissions);
        }

        $this->clearRelatedCache();
    }

    /**
     * Clear all caches related to this role.
     *
     * @return void
     */
    public function clearRelatedCache()
    {
        $users = $this->users;
        foreach ($users as $user) {
            $user->clearCachedRoles();
            $user->clearCachedPermissions();
        }
    }
}
