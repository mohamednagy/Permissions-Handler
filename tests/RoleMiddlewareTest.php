<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler;
use Illuminate\Support\Facades\Route;
use PermissionsHandler\Middleware\RoleMiddleware;

class RoleMiddlewareTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Route::group(['namespace' => 'PermissionsHandler\Tests\Controllers'], function () {
            Route::get('/index', 'RoleTestController@index')->middleware(RoleMiddleware::class.':user');
            Route::get('/hasOneRole', 'RoleTestController@index')->middleware(RoleMiddleware::class.':user|admin');
            Route::get('/hasNoRole', 'RoleTestController@index')->middleware(RoleMiddleware::class.':user|admin, true');
        });
    }

    /** @test */
    public function a_user_can_access_route_if_he_has_a_role()
    {
        $this->userModel->assignRole($this->userRoleModel);
        $this->actingAs($this->userModel);
        $response = $this->get('/index');
        $response->assertSee('accessed');
    }

    /** @test */
    public function a_user_can_access_route_if_he_has_at_least_one_role()
    {
        $this->userModel->assignRole($this->userRoleModel);
        $this->actingAs($this->userModel);
        $response = $this->get('/hasOneRole');
        $response->assertSee('accessed');
    }

    /** @test */
    public function a_user_can_access_route_if_he_has_all_roles()
    {
        $this->userRoleModel->assignPermission($this->userRoleModel);
        $this->userModel->assignRole($this->userRoleModel);
        $this->actingAs($this->userModel);
        $response = $this->get('/hasNoRole');
        $response->assertStatus(403);
    }
}
