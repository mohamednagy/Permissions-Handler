<?php

namespace PermissionsHandler\Tests;


use PermissionsHandler;
use Illuminate\Support\Facades\Route;
use PermissionsHandler\Middleware\PermissionMiddleware;
use PermissionsHandler\Tests\Controllers\TestController;
use PermissionsHandler\Tests\Models\Post;


class PermissionMiddlewareTest extends TestCase {


    protected $permissionMiddleware;

    public function setUp()
    {
        parent::setUp();

        Route::group(['namespace' => 'PermissionsHandler\Tests\Controllers'], function() {
            Route::get('/index', 'PermissionTestController@index')->middleware(PermissionMiddleware::class.':userPermission');
            Route::get('/hasOnePermission', 'PermissionTestController@index')->middleware(PermissionMiddleware::class.':userPermission|noExistingPermission');
            Route::get('/hasNoPermission', 'PermissionTestController@index')->middleware(PermissionMiddleware::class.':userPermission|noExistingPermission, true');
        });
        
    }

    /** @test */
    public function a_user_can_access_route_if_he_has_a_permission()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->userModel->assignRole($this->userRoleModel);
        $this->actingAs($this->userModel);
        $response = $this->get('/index');
        $response->assertSee('accessed');
    }


    /** @test */
    public function a_user_can_access_route_if_he_has_at_least_one_permission()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->userModel->assignRole($this->userRoleModel);
        $this->actingAs($this->userModel);
        $response = $this->get('/hasOnePermission');
        $response->assertSee('accessed');
    }


    /** @test */
    public function a_user_can_access_route_if_he_has_all_permissions()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->userModel->assignRole($this->userRoleModel);
        $this->actingAs($this->userModel);
        $response = $this->get('/hasNoPermission');
        $response->assertStatus(403);
    }




    

}