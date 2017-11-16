<?php

namespace PermissionsHandler\Exceptions;

use InvalidArgumentException;

class RoleAlreadyExists extends InvalidArgumentException
{
    public static function create(string $roleName)
    {
        return new static("A role `{$roleName}` already exists.");
    }
}