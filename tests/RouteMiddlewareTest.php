<?php

namespace PermissionsHandler\Tests;


use PermissionsHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Validation\UnauthorizedException;
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

        $this->request = Request::create('http://permissions.dev', 'GET');
    }
    /** @test */
    public function a_guest_cannot_access_a_route_protected_by_the_role_middleware()
    {
        $this->assertEquals(
            $this->runMiddleware(
                $this->routeMiddleware, 'permissions@userPermission', 'roles@user'
            ), 403);
    }

    /** @test */
    public function a_user_can_access_a_route_protected_by_route_middleware_if_have_this_role()
    {
        \Auth::login($this->testUser);
        PermissionsHandler::assignRoleToUser($this->testUserRole, $this->testUser);
        $this->assertEquals(
            $this->runMiddleware(
                $this->routeMiddleware, ['permissions@add-user;edit-user' , 'roles@testRole']
            ), 200);
    }

    /** @test */
    public function a_user_can_not_access_a_route_protected_by_route_middleware_if_have_not_this_role()
    {
        \Auth::login($this->testUser);
        PermissionsHandler::assignRoleToUser($this->testUserRole, $this->testUser);
        $this->assertEquals(
            $this->runMiddleware(
                $this->routeMiddleware, 'permissions@add-user;edit-user,roles@testRole;'
            ), 403);
    }

    protected function runMiddleware($middleware, ...$parameters)
    {
        try {
            $middlewareParameters = [
                $this->request,
                function () {
                    return (new Response())->setContent('<html></html>');
                }
            ];
            $middlewareParameters = array_merge($middlewareParameters, $parameters);
            call_user_func_array([$middleware, 'handle'], $middlewareParameters);

        } catch (\PermissionsHandler\Exceptions\UnauthorizedException $e) {
            return $e->getStatusCode();
        }
    }
}