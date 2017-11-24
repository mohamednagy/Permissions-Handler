<?php

namespace PermissionsHandler;

use PermissionsHandler\Annotations\Checkable;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Roles implements Checkable
{
    public $roles;

    public $requireAll = false;

    public function check()
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        return $user->hasRole($this->roles, $this->requireAll);
    }
}
