<?php
/**
 * @canDo
 *
 * @param array|string $permissions
 */
Blade::if('canDo', function ($permissions) {
    $user = app('Illuminate\Http\Request')->user();
    if (!$user) {
        return false;
    }

    return $user->hasPermission($permissions);
});

/*
 * @hasPermissions
 *
 * @param array|string $permissions
 */
Blade::if('hasPermissions', function ($permissions) {
    $user = app('Illuminate\Http\Request')->user();
    if (!$user) {
        return false;
    }

    return $user->hasPermission($permissions);
});

/*
 * @hasRole
 *
 * @param string $role
 */
Blade::if('hasRole', function ($role) {
    $user = app('Illuminate\Http\Request')->user();
    if (!$user) {
        return false;
    }

    return $user->hasRole($role);
});
