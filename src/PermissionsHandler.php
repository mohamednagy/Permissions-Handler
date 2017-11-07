<?php

namespace PermissionsHandler;

/*
 * @Auther: Mohamed Nagy
 * @version : 1.0
 */
use PermissionsHandler\Traits\PermissionsHandlerCacheTrait;
use Doctrine\Common\Annotations\FileCacheReader;
use Doctrine\Common\Annotations\AnnotationReader;

class PermissionsHandler
{
    use PermissionsHandlerCacheTrait;

    private $user;
    private $annotationReader;
    private $config = array();

    public function __construct($user)
    {
        $this->user = $user;
        $this->annotationReader = new FileCacheReader(
            new AnnotationReader(),
            storage_path('PermissionsHandler/Cache'),
            (strpos(strtolower(env('APP_ENV')), 'prod') === false)
        );

        $this->config = config('permissionsHandler');
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
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }
        $roles = $this->getUserRoles();
        $allPermissions = $this->getRolePermissions($roles);
        $hasPermission = array_intersect($allPermissions, $permissions);
        if ($this->config['aggressiveMode'] == true) {
            return count($hasPermission) == count($permissions);
        }
        return count($hasPermission) > 0;
    }

    /**
     * check if a user has permission to access specific route.
     *
     * @return bool
     */
    public function canGo()
    {
        $request = app("Illuminate\Http\Request");
        if ($this->isExcludedRoute($request)) {
            return true;
        }
        $permissions = $this->getPermissionsFromAnnotations($request);
        if ($this->config['aggressiveMode'] == true && empty($permissions)) {
            return false;
        } elseif ($this->config['aggressiveMode'] == false && empty($permissions)) {
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
    public function isExcludedRoute($request)
    {
        return in_array($request->path(), $this->config['excludedRoutes']);
    }

    /**
     * get the assigned permissins from the method annotaions
     *
     * @param Illuminate\Http\Request $request
     * @return void
     */
    public function getPermissionsFromAnnotations($request)
    {
        $permFromAnnot = [];
        $actionName = $request->route()->getActionName();
        if (strpos($actionName, '@') !== false) {
            $action = explode('@', $actionName);
            $class = $action[0];
            $method = $action[1];
            $reflectionMethod = new \ReflectionMethod($class, $method);
            $permissions = $this->annotationReader->getMethodAnnotations($reflectionMethod);
            if (isset($permissions[0]) && $permissions[0]->permissions) {
                $permFromAnnot = $permissions[0]->permissions['value'];
            }
        }
        return $permFromAnnot;
    }
}
