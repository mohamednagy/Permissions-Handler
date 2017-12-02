<?php

namespace PermissionsHandler\Exceptions;

use InvalidArgumentException;

class RoleNotFound extends InvalidArgumentException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
