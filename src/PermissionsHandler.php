<?php

namespace PermissionsHandler;

/*
 * @Auther: Mohamed Nagy
 * @version : 1.0
 */
use PermissionsHandler\CanDo;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Models\Permission;
use Doctrine\Common\Annotations\AnnotationReader;
use PermissionsHandler\PermissionsHandlerInterface;

class PermissionsHandler implements PermissionsHandlerInterface
{

    private $user;
    private $annotationReader;

    public function __construct($user)
    {
        $this->user = $user;
        $this->annotationReader = new AnnotationReader();
    }

    /**
    * check if a user has permissions
    *
    * @param array $permissions
    *
    * @return bool
    */
    function hasPermissions($permissions = [])
    {
        $hasPermission = $this->user->whereHas('roles', function($roles) use ($permissions){
            $roles->whereHas('permissions', function($query) use ($permissions){
                $query->whereIn('name', $permissions);
            });
        })->count();
        return $hasPermission > 0;
    }

    /**
     * check if a user has permission to access specific route.
     *
     * @return bool
     */
    public function can()
    {
        $request = app("Illuminate\Http\Request");
        if($this->isExcludedRoute($request)){
            return true;
        }
        $permissions = $this->getPermissionsFromAnnotations($request);
        if(config('permissionsHandler.aggressiveMode') == true && empty($permissions)){
            return false;
        }elseif(config('permissionsHandler.aggressiveMode') == false && empty($permissions)){
            return true;
        }
        return $this->hasPermissions($permissions);
    }
    
    /**
     * check if the current route is excluded from permissions rules
     *
     * @param Illuminate\Http\Request $request
     * @return boolean
     */
    public function isExcludedRoute($request){
        $excludedRoutes = config('permissionsHandler.excludedRoutes');
        return in_array($request->path(), $excludedRoutes);
    }

    /**
     * get the assigned permissins from the method annotaions
     *
     * @param Illuminate\Http\Request $request
     * @return void
     */
    public function getPermissionsFromAnnotations($request){
        $permFromAnnot = [];
        $actionName = $request->route()->getActionName();
        if (strpos($actionName, '@') !== false) {
            $action = explode('@', $actionName);
            $class = $action[0];
            $method = $action[1];
            $reflectionMethod = new \ReflectionMethod($class, $method);
            $permissions = $this->annotationReader->getMethodAnnotations($reflectionMethod);
            if(isset($permissions[0]) && $permissions[0]->permissions){
                $permFromAnnot = $permissions[0]->permissions['value'];
            }
        }
        return $permFromAnnot;

    }
}
