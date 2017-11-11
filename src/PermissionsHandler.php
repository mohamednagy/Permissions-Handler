<?php

namespace PermissionsHandler;

/*
 * @Auther: Mohamed Nagy
 */
use PermissionsHandler\Traits\CrudTrait;
use Doctrine\Common\Annotations\FileCacheReader;
use Doctrine\Common\Annotations\AnnotationReader;

class PermissionsHandler
{
    use CrudTrait;
    
    protected $annotationReader;
    protected $config = array();

    
    public function __construct()
    {
        $this->annotationReader = new FileCacheReader(
            new AnnotationReader(),
            storage_path('PermissionsHandler/Cache'),
            (strpos(strtolower(env('APP_ENV')), 'prod') === false)
        );

        $this->config = config('permissionsHandler');
    }

    /**
     * check if a user has permission to access specific route.
     *
     * @return bool
     */
    public function canGo($request)
    {
        if ($this->isExcludedRoute($request)) {
            return true;
        }
        $annotations = $this->getAnnotationsFromRequest($request);
        if ($this->config['aggressiveMode'] == true && empty($annotations)) {
            return false;
        } elseif ($this->config['aggressiveMode'] == false && empty($annotations)) {
            return true;
        }
        foreach($annotations as $annotation){
            if(!$annotation->check($this->config['aggressiveMode'])){
                return  false;
            }
        }
        return true;

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
     * get the assigned annotations from the a route
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function getAnnotationsFromRequest($request)
    {
        $annotations = [];
        $actionName = $request->route()->getActionName();
        if (strpos($actionName, '@') !== false) {
            $action = explode('@', $actionName);
            $class = $action[0];
            $method = $action[1];
            $reflectionMethod = new \ReflectionMethod($class, $method);
            $annotations = $this->annotationReader->getMethodAnnotations($reflectionMethod);
        }
        return $annotations;
    }


    /**
     * clear all cached annotations
     *
     * @return void
     */
    public function clearAnnotationsCache()
    {
        $this->annotationReader->clearLoadedAnnotations();
    }
}
