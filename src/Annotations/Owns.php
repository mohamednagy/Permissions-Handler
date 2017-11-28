<?php

namespace PermissionsHandler;

use PermissionsHandler\Annotations\Checkable;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Owns implements Checkable
{
    public $relation;

    public $parameter;

    public $key;


    public function check()
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return $user->owns($this->relation, $this->parameter, $this->key);
    }
}