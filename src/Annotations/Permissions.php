<?php

namespace PermissionsHandler;

use PermissionsHandler\Annotations\Checkable;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Permissions implements Checkable
{
    public $permissions;

    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }

    public function check($isAggressive)
    {
        $permissions = $this->permissions['value'];
        $user = app('Illuminate\Http\Request')->user();

        $result = false;
        foreach ($permissions as $permission) {
            $hasPermission = $user->hasPermission($permission);
            if ($isAggressive == true && $hasPermission == false) {
                return false;
            }
            if ($hasPermission == true) {
                $result = true;
            }
        }

        return $result;
    }
}
