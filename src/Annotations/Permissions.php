<?php

namespace PermissionsHandler;

use PermissionsHandler\Annotations\AbstractCheck;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Permissions extends AbstractCheck
{
    public $permissions;

    public $requireAll = false;

    public function __construct($permissions, $requireAll = false)
    {
        $this->permissions = $permissions;
        $this->requireAll = $requireAll;
    }

    public function check()
    {
        $user = $this->getUserFromGuards();

        if (! $user) {
            return false;
        }

        return $user->hasPermission($this->permissions, $this->requireAll);
    }
}
