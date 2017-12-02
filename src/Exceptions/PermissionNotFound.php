<?php

namespace PermissionsHandler\Exceptions;

use InvalidArgumentException;

class PermissionNotFound extends InvalidArgumentException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
