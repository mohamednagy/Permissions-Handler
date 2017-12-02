<?php
/**
 * @canDo
 *
 * @param string $permission
 */
Blade::if('canDo', function ($permission) {
    $user = auth()->user();
    if (! $user) {
        return false;
    }

    return $user->hasPermission($permission);
});

/*
 * @hasPermission
 *
 * @param $permission
 */
Blade::if('permission', function ($permissions, $requireAll = false) {
    $user = auth()->user();
    if (! $user) {
        return false;
    }

    return $user->hasPermission($permissions, $requireAll);
});

/*
 * @hasRole
 *
 * @param string $role
 */
Blade::if('role', function ($roles, $requireAll = false) {
    $user = auth()->user();
    if (! $user) {
        return false;
    }

    return $user->hasRole($role, $requireAll);
});
