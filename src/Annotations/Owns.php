<?php

namespace PermissionsHandler;

use PermissionsHandler\Annotations\AbstractCheck;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Owns extends AbstractCheck
{
    public $relation;

    public $parameter;

    public $key;

    public function check()
    {
        $user = $this->getUserFromGuards();
        if (! $user) {
            return false;
        }

        return $user->owns($this->relation, $this->parameter, $this->key);
    }
}
