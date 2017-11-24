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

    public $requireAll = false;


    public function check()
    {

        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->hasPermission($this->permissions, $this->requireAll);
    }

    

    
}
