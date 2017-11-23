<?php

namespace PermissionsHandler\Seeder;

use Illuminate\Support\Facades\Storage;


class Seeder {

    /**
     * Save the created permission to the permissin file
     *
     * @param PermissionsHandler\Models\Permission $permission
     * @return void
     */
    public static function seedPermission($permission)
    {
        $permissions = self::getFileContent('permissions.json');
        
        if (!in_array($permission->name, $permissions)) {
            $permission = $permission->toArray();
            unset($permission['id']);
            $permissions[] = $permission;
            self::saveFileContent('permissions.json', $permissions);
        }
    }

    /**
     * Save the created role into a role file
     *
     * @param PermissionsHandler\Models\Role $role
     * @return void
     */
    public static function seedRole($role)
    {
        $roles = self::getFileContent('roles.json');
        
        if (!in_array($role->name, $roles)) {
            $role = $role->toArray();
            unset($role['id']);
            $roles[] = $role;
            self::saveFileContent('roles.json', $roles);
        }
    }

    /**
     * Save the assigned permissions to role
     *
     * @param string $roleName
     * @param \Illuminate\Support\Collection|array $permissions
     * @return void
     */
    public static function assignPermissionsToRole($role, $permissions)
    {
        if (!$permissions instanceof \Illuminate\Support\Collection) {
            $permissions  = is_array($permissions) ? collect($permissions) : collect([$permissions]);
        }

        $permissions = $permissions->pluck('name')->toArray();
        
        $all = self::getFileContent('role-permissions.json');

        $rolePermissions = array_unique(array_merge($permissions, $role->permissions->pluck('name')->toArray()));
        $all[$role->name] = $rolePermissions;
        self::saveFileContent('role-permissions.json', $all);
    }

    /**
     * Remove all assigned permission from specific role
     *
     * @param PermissionsHandler\Models\Role $role
     * @return void
     */
    public static function unAssignAllRolePermissions($role)
    {
        $all = self::getFileContent('role-permissions.json');
        if (isset($all[$role->name])) {
            $all[$role->name] = [];
        }
        self::saveFileContent('role-permissions.json', $all);

    }


    /**
     * Remove some assigned permissions from specific roles
     *
     * @param PermissionsHandler\Models\Role $role
     * @param \Illuminate\Support\Collection|array $permissions
     * @return void
     */
    public static function unAssignRolePermissions($role, $permissions)
    {
        if (!$permissions instanceof \Illuminate\Support\Collection) {
            $permissions  = is_array($permissions) ? collect($permissions) : collect([$permissions]);
        }

        $permissions = $permissions->pluck('name')->toArray();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $result = array_diff($rolePermissions, $permissions);
        
        $all = self::getFileContent('role-permissions');
        $all[$role->name] = $result;
        
        self::saveFileContent('role-permissions.json', $all);
    }

    /**
     * Get file content from the storage, if the file doesn't exists return empty array
     *
     * @param string $file
     * @return array
     */
    public static function getFileContent($file)
    {
        $content = [];

        if (Storage::disk('permissions')->exists($file)) {
            $content = Storage::disk('permissions')->get($file);
            $content = json_decode($content, true);
        }

        return $content;
    }


    /**
     * Save content to file
     *
     * @param string $file
     * @param array|string $content
     * @return void
     */
    public static function saveFileContent($file, $content)
    {
        $content = is_array($content) ? json_encode($content) : $content;
        Storage::disk('permissions')->put($file, $content);
    }


}
