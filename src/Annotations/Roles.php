<?php

namespace PermissionsHandler;

use PermissionsHandler\Annotations\Checkable;

/**
* @Annotation
* @Target("METHOD")
*/
class Roles implements Checkable
{
    public $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function check($isAggressive)
    {
        $roles = $this->roles['value'];
        $user = app('Illuminate\Http\Request')->user();
        $result = false;
        foreach ($roles as $role) {
            $hasRole = $user->hasRole($role);
            if ($isAggressive == true && $hasRole == false) {
                return false;
            }
            if($hasRole){
                $result = true;
            }
        }
        return $result;
    }
}
