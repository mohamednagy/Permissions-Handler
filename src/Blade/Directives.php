<?php
/**
 * @canDo
 *
 * @param string $permission
 */
Blade::if('canDo', function ($permission) {
    $user = app('Illuminate\Http\Request')->user();
    if (!$user) {
        return false;
    }

    return $user->hasPermission($permission);
});

/*
 * @hasPermission
 *
 * @param $permission
 */
Blade::if('hasPermission', function ($permission) {
    $user = app('Illuminate\Http\Request')->user();
    if (!$user) {
        return false;
    }

    return $user->hasPermission($permission);
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
