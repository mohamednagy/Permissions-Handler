<?php

namespace PermissionsHandler\Annotations;

abstract class AbstractCheck
{
    public abstract  function check();
    
    public function getUserFromGuards()
    {
        $user = null;
        $guards = config('auth.guards');
        foreach ($guards as $guard => $data) {
            $user = auth()->guard($guard)->user();
            if ($user) {
                break;
            }
        }
        
        return $user;
    }
}
