<?php

namespace PermissionsHandler\Tests;


use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use PermissionsHandler\Middleware\RouteMiddlleware;
use PermissionsHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RouteMiddlewareTest extends TestCase
{
    protected $routeMiddleware;

    public function setUp()
    {
        parent::setUp();
        $this->routeMiddleware = new RouteMiddlleware(\Auth::guard('web'));
    }
    /** @test */
    public function a_guest_cannot_access_a_route_protected_by_the_role_middleware()
    {
        $this->assertEquals(
            $this->runMiddleware(
                $this->routeMiddleware, 'permissions@add-user;edit-user,roles@admin'
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

    protected function runMiddleware($middleware, array $parameters)
    {
        try {
            return $middleware->handle(new \Request(), function () {
                return (new Response())->setContent('<html></html>');
            }, $parameters[0], $parameters[1])->status();
        } catch (\PermissionsHandler\Exceptions\UnauthorizedException $e) {
            return $e->getStatusCode();
        }
    }
}