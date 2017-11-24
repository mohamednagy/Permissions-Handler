<?php

namespace PermissionsHandler\Tests\Controllers;

use PermissionsHandler\Roles;
use Illuminate\Routing\Controller;
use PermissionsHandler\Permissions;
use PermissionsHandler\Middleware\MethodMiddleware;

class TestController extends Controller
{

    public function __construct()
    {
        $this->middleware(MethodMiddleware::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function index()
    {
        return 'accessed';
    }

    /**
     * a user that has role `user` can access this method 
     * 
     * @Roles({"admin"})
     * 
     * @return boolean
     */
    public function checkAdminRole()
    {
        return 'accessed';
    }


    /**
     * a user must has all assigned roles
     *
     * @Roles({"admin", "notExistingRole"}, requireAll=true)
     * 
     * @return void
     */
    public function mustHasAllRoles()
    {
        return 'accessed';
    }

    /**
     * a user with permissoin adminPermission can access this method
     *
     * @Permissions({"adminPermission", "notExistsPermission"})
     * 
     * @return void
     */
    public function checkAdminPermission()
    {
        return 'accessed';
    }


    /**
     * a user must has all assigned Permissions
     *
     * @Permissions({"userPermission", "notExistingPermission"}, requireAll=true)
     * 
     * @return void
     */
    public function mustHasAllPermissions()
    {
        return 'accessed';
    }

}

