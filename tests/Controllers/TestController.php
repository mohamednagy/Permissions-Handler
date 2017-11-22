<?php

namespace PermissionsHandler\Tests\Controllers;

use Illuminate\Routing\Controller;
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
     * @\PermissionsHandler\Roles({"admin"})
     * 
     * @return boolean
     */
    public function checkAdminRole()
    {
        return 'accessed';
    }

    /**
     * a user with permissoin adminPermission can access this method
     *
     * @\PermissionsHandler\Permissions({"adminPermission"})
     * 
     * @return void
     */
    public function checkAdminPermission()
    {
        return 'accessed';
    }

}

