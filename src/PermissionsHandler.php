<?php

namespace PermissionsHandler;

/*
 * @Auther: Mohamed Nagy
 * @version : 2.0
 */
use PermissionsHandler\PermissionsHandlerInterface;

use PermissionsHandler\DocBlockReader\Reader;
use PermissionsHandler\HtmlDomParser\Htmldom;

use PermissionsHandler\Models\Role;
use PermissionsHandler\Models\Permission;

class PermissionsHandler implements PermissionsHandlerInterface
{
    private $message;

    private $user;

    public function __construct()
    {
      $this->user = config('permissionsHandler.user');
    }


    /**
    * set message
    *
    * @param $message  string
    */
    function setMessage($msg){
      $this->message = $msg;
    }


    /**
    * get message
    *
    * @return string
    */
    function getMessage(){
      return $this->message;
    }


    /**
     * set the user model
     */
    public function setUser($user){
      $this->user = $user;
      $this->_getPermissions();
    }



    /**
    * check if a user has specfic permissions
    *
    * @param $permissions  array
    *        $user         App\Models\User
    *
    * @return bool
    */
    function hasPermissions($permissions = []){
      if(!is_array($permissions)){
        $permissions = [$permissions];
      }
      $roles = $this->user->roles;
      foreach ($roles as $role) {
        $permissions = $role->permissions()->whereIn('name', $permissions)->get();
        if(!$permissions->isEmpty()){
          return true;
        }
      }
      return false;
    }


    /**
     * parse response against permissions to slice elements that the user doesnot have permissions.
     *
     * @param $str  Response
     * @param $user User model
     *
     * @return Response
     */
    public function parseView($str, $user = null)
    {
        if (!$user) {
            $user = \Auth::user();
        }
        $this->user = $user;
        $html = new Htmldom($str);
        $elems = $html->find('*[permissions]');
        foreach ($elems as $elem) {
            $permissions = $elem->attr['permissions'];
            if (!$this->hasPermissions([$permissions])) {
                $elem->outertext = '';
            }
        }

        return $html->save();
    }

    /**
     * check if a user has permission to access specific route.
     *
     * @param string $methodName used as ReflectionMethod instance
     * @param Model  $user
     *
     * @return bool
     */
    public function can()
    {
        $request = app("Illuminate\Http\Request");
        $actionName = $request->route()->getActionName();
        if (strpos($actionName, '@') !== false) {
            $array = explode('@', $actionName);
            $class = $array[0];
            $method = $array[1];
            $reader = new Reader($class, $method);

            // if permissions assiged
            $permissions = $reader->getParameter('permissions');
            if (is_array($permissions) && $permissions) {
                return $this->hasPermissions($permissions);
            }
        }
        return true;
    }
}
