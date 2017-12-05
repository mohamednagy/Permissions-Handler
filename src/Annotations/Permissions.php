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

        if (count($this->permissions) == 2 && isset($this->permissions['value'])) {
            $this->requireAll = is_bool($this->permissions['requireAll']) ? $this->permissions['requireAll'] : $this->requireAll;
        }

        $this->permissions = isset($this->permissions['value']) ? $this->permissions['value'] :  $this->permissions;
        
        return $user->hasPermission($this->permissions, $this->requireAll);
    }
}
