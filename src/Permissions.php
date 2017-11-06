<?php
namespace PermissionsHandler;

/**
* @Annotation
* @Target("METHOD")
* @Attributes({
* })
*/
final class Permissions
{
    public $permissions;

    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }
}
