<?php

namespace PermissionsHandler;

use PermissionsHandler\Annotations\AbstractCheck;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Roles extends AbstractCheck
{
    public $roles;

    public $requireAll = false;

    public function __construct($roles, $requireAll = false)
    {
        $this->roles = $roles;
        $this->requireAll = $requireAll;
    }

    public function check()
    {
        $user = $this->getUserFromGuards();

        if (! $user) {
            return false;
        }

        if (count($this->roles) == 2 && isset($this->roles['value'])) {
            $this->requireAll = is_bool($this->roles['requireAll']) ? $this->roles['requireAll'] : $this->requireAll;
        }

        $this->roles = isset($this->roles['value']) ? $this->roles['value'] : $this->roles;

        return $user->hasRole($this->roles, $this->requireAll);
    }
}
