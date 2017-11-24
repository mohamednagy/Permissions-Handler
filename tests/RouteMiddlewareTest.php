<?php

namespace PermissionsHandler\Tests;


use PermissionsHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Auth\Guard;
use PermissionsHandler\Middleware\RouteMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RouteMiddlewareTest extends TestCase
{
    protected $routeMiddleware;
    protected $request;

    public function setUp()
    {
        parent::setUp();

        $this->routeMiddleware = new RouteMiddleware();

        $this->request = Request::create('/', 'GET');

    }

    /** @test */
    public function a_guest_cannot_access_a_route_protected_by_the_route_middleware()
    {
        $status_code = null;
        try{
            $response = $this->routeMiddleware->handle(
                $this->request,
                function () { 
                    return new Response();
                },
                'permissions@userPermissions', 'roles@admin'
            );
        }
        catch(\Exception $e){
            $status_code = $e->getStatusCode();
        }
        $this->assertEquals($status_code, 403);
        
    }

    /** @test */
    public function a_user_can_access_a_route_protected_by_route_middleware_if_has_this_role()
    {
        $this->adminModel->assignRole($this->adminRoleModel);
        $this->actingAs($this->adminModel);
        
        $status_code = null;
        try{
            $response = $this->routeMiddleware->handle(
                $this->request,
                function () { 
                    return new Response();
                },
                'roles@admin'
            );
        }
        catch(\Exception $e){
            $status_code = $e->getStatusCode();
        }
        $this->assertNotEquals($status_code, 403);
    }

    /** @test */
    public function a_user_can_not_access_a_route_protected_by_route_middleware_if_have_not_this_role()
    {
        $this->adminModel->assignRole($this->adminRoleModel);
        $this->actingAs($this->adminModel);
        $status_code = null;
        try{
            $response = $this->routeMiddleware->handle(
                $this->request,
                function () { 
                    return new Response();
                },
                'roles@notExistsRole'
            );
        }
        catch(\Exception $e){
            $status_code = $e->getStatusCode();
        }
        $this->assertEquals($status_code, 403);
    }


    /** @test */
    public function a_user_can_access_a_route_protected_by_route_middleware_if_has_this_permission()
    {
        $this->adminModel->assignRole($this->adminRoleModel);
        $this->adminRoleModel->assignPermission($this->adminPermissionModel);
        $this->actingAs($this->adminModel);
        $status_code = null;
        try{
            $response = $this->routeMiddleware->handle(
                $this->request,
                function () { 
                    return new Response();
                },
                'permissions@adminPermission'
            );
        }
        catch(\Exception $e){
            $status_code = $e->getStatusCode();
        }
        $this->assertNotEquals($status_code, 403);
    }


    /** @test */
    public function a_user_can_not_access_a_route_protected_by_route_middleware_if_not_has_this_permission()
    {
        $this->userModel->assignRole($this->userRoleModel);
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->actingAs($this->userModel);
        $status_code = null;
        try{
            $response = $this->routeMiddleware->handle(
                $this->request,
                function () { 
                    return new Response();
                },
                'permissions@notExistsPermission'
            );
        }
        catch(\Exception $e){
            $status_code = $e->getStatusCode();
        }
        $this->assertEquals($status_code, 403);
    }
}