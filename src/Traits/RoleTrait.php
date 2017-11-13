<?php

namespace PermissionsHandler\Traits;

trait RoleTrait
{
    /**
     * Delete role
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

    /**
     * Assign permission to role
     *
     * @param \Illuminate\Database\Eloquent\Model $permission
     * @return void
     */
    public function assignPermission($permission)
    {
        $permissions = $this->permissions->pluck('id')->toArray();
        if (in_array($permission->id, $permissions)) {
            return false;
        }
        $this->permissions()->attach($permission->id);
        $this->clearRelatedCache();
    }


    /**
     * Assign many permission to a role
     *
     * @param array $permissions
     * @return void
     */
    public function assignPermissions($permissions = [])
    {
        $rolePermissions = $this->permissions->pluck('id')->toArray();
        foreach ($permissions as $permission) {
            $rolePermissions[] = $permission->id;
        }
        $this->permissions()->sync($rolePermissions);

        $this->clearRelatedCache();
    }


    /**
     * Remove all permission from a role
     *
     * @return void
     */
    public function unAssignAllPermission()
    {
        $this->permissions()->sync([]);
        $this->clearRelatedCache();
    }


    /**
     * Unassign permission from role
     *
     * @param \Illuminate\Database\Eloquent\Model $permission
     * @return void
     */
    public function unAssignPermission($permission)
    {
        $this->permissions()->detach($permission->id);
        $this->clearRelatedCache();
    }

    /**
     * Unassign many permission from a role
     *
     * @param array $permissions
     * @return void
     */
    public function unAssignPermissions($permissions = [])
    {
        $rolePermissions = $this->permissions->pluck('id')->toArray();
        foreach ($permissions as $permission) {
            if ($key = array_search($permission->id, $rolePermissions) !== false) {
                unset($rolePermissions[$key]);
            }
        }
        $this->permissions()->sync($rolePermissions);

        $this->clearRelatedCache();
    }


    /**
     * Clear all caches related to this role
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
