<?php
/**
 * @canDo
 * 
 * @param array|string $permissions
 */
Blade::if('canDo', function ($permissions) {
    return \PermissionsHandler::hasPermissions($permissions);
});


/**
 * @hasPermissions
 * 
 * @param array|string $permissions
 */
Blade::if('hasPermissions', function ($permissions) {
    return \PermissionsHandler::hasPermissions($permissions);
});


/**
 * @hasRole
 * 
 * @param string $role
 */
Blade::if('hasRole', function ($role) {
    return auth()->user()->hasRole($role);
});


?>