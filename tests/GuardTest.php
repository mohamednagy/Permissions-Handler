<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler;
use Illuminate\Support\Facades\Route;
use PermissionsHandler\Middleware\RoleMiddleware;

class GuardTest extends TestCase
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
    public function a_user_with_guard_admins_and_role_admin_can_access_the_method()
    {
        $this->adminModel->assignRole($this->adminRoleModel);
        auth()->guard('admins')->login($this->adminModel);
        $response = $this->get('/hasOneRole');
        $response->assertSee('accessed');
    }

    /** @test */
    public function a_user_with_guard_web_can_not_access_method_protected_by_admin_role_in_guard_admins()
    {
        $this->adminModel->assignRole($this->adminRoleModel);
        auth()->guard('web')->login($this->userModel);
        $response = $this->get('/hasOneRole');
        $response->assertStatus(403);
    }
}