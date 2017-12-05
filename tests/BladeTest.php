<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler;
use PermissionsHandler\Models\Role;
use Illuminate\Support\Facades\Artisan;
use PermissionsHandler\Models\Permission;

class BladeTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function all_blade_templates_should_assert_false_when_no_one_logged_in()
    {
        $permission = 'test-permission';
        $role = 'test-role';

        $this->assertEquals('has no permission', $this->renderView('can', ['permission' => $permission]));
        $this->assertEquals('has no permission', $this->renderView('permission', ['permission' => $permission]));
        $this->assertEquals('has no role', $this->renderView('role', ['role' => $role]));
    }

    /** @test */
    public function can_directive_returns_true_if_a_user_has_a_permission()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->userModel->assignRole($this->userRoleModel);
        auth()->setUser($this->userModel);

        $this->assertEquals('has permission', $this->renderView('can', ['permission' => $this->userPermissionModel->name]));
    }

    /** @test */
    public function can_directive_returns_false_if_a_user_doesnot_has_a_permission()
    {
        $permission = Permission::create(['name' => 'test-permission']);
        $this->userModel->assignRole($this->userRoleModel);
        auth()->setUser($this->userModel);

        $this->assertEquals('has no permission', $this->renderView('can', ['permission' => $permission->name]));
    }

    /** @test */
    public function permission_directive_returns_true_if_a_user_has_a_permission()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->userModel->assignRole($this->userRoleModel);
        auth()->setUser($this->userModel);

        $this->assertEquals('has permission', $this->renderView('permission', ['permission' => $this->userPermissionModel->name]));
    }

    /** @test */
    public function permission_directive_returns_true_if_a_user_has_array_of_permissions_as_string()
    {
        $permission1 = Permission::create(['name' => 'permission1']);
        $permission2 = Permission::create(['name' => 'permission2']);

        $this->userRoleModel->assignPermission($permission1, $permission2);
        $this->userModel->assignRole($this->userRoleModel);

        $this->actingAs($this->userModel);

        $this->assertEquals('has permission', $this->renderView('permission', ['permission' => ['permission1', 'permission2']]));
    }

    /** @test */
    public function permission_directive_returns_true_if_a_user_has_array_of_permissions_as_models()
    {
        $permission1 = Permission::create(['name' => 'permission1']);
        $permission2 = Permission::create(['name' => 'permission2']);

        $this->userRoleModel->assignPermission($permission1, $permission2);
        $this->userModel->assignRole($this->userRoleModel);

        $this->actingAs($this->userModel);

        $this->assertEquals('has permission', $this->renderView('permission', ['permission' => [$permission1, $permission2]]));
    }

    /** @test */
    public function permission_directive_returns_true_if_a_user_has_all_array_of_permissions_as_models()
    {
        $permission1 = Permission::create(['name' => 'permission1']);
        $permission2 = Permission::create(['name' => 'permission2']);

        $this->userRoleModel->assignPermission($permission1, $permission2);
        $this->userModel->assignRole($this->userRoleModel);

        $this->actingAs($this->userModel);

        $viewData = ['permission' => [$permission1, $permission2], 'requireAll' => true];
        $this->assertEquals('has permission', $this->renderView('requireAllPermissions', $viewData));
    }


    /** @test */
    public function permission_directive_returns_false_if_a_user_dosent_has_all_array_of_permissions_as_models()
    {
        $permission1 = Permission::create(['name' => 'permission1']);
        $permission2 = Permission::create(['name' => 'permission2']);

        $this->userRoleModel->assignPermission($permission1);
        $this->userModel->assignRole($this->userRoleModel);

        $this->actingAs($this->userModel);

        $viewData = ['permission' => [$permission1, $permission2], 'requireAll' => true];
        $this->assertEquals('has no permission', $this->renderView('requireAllPermissions', $viewData));
    }

    /** @test */
    public function role_directive_returns_true_if_a_user_has_a_role()
    {
        $this->userModel->assignRole($this->userRoleModel);
        auth()->setUser($this->userModel);

        $this->assertEquals('has role', $this->renderView('role', ['role' => $this->userRoleModel]));
    }

    /** @test */
    public function role_directive_returns_true_if_a_user_has_role_as_string()
    {
        $this->userModel->assignRole($this->userRoleModel);
        auth()->setUser($this->userModel);

        $this->assertEquals('has role', $this->renderView('role', ['role' => $this->userRoleModel->name]));
    }

    /** @test */
    public function role_directive_returns_true_if_a_user_has_role_as_array_string()
    {
        $role1 = Role::create(['name' => 'role1']);
        $role2 = Role::create(['name' => 'role2']);

        $this->userModel->assignRole($role1, $role2);
        auth()->setUser($this->userModel);

        $this->assertEquals('has role', $this->renderView('role', ['role' => ['role1', 'role2']]));
    }

    /** @test */
    public function role_directive_returns_true_if_a_user_has_role_as_array_models()
    {
        $role1 = Role::create(['name' => 'role1']);
        $role2 = Role::create(['name' => 'role2']);

        $this->userModel->assignRole($role1, $role2);
        auth()->setUser($this->userModel);

        $this->assertEquals('has role', $this->renderView('role', ['role' => [$role1, $role2]]));
    }

    /** @test */
    public function role_directive_returns_true_if_a_user_has_all_role_as_array_models()
    {
        $role1 = Role::create(['name' => 'role1']);
        $role2 = Role::create(['name' => 'role2']);

        $this->userModel->assignRole($role1, $role2);
        auth()->setUser($this->userModel);

        $viewData = ['role' => [$role1, $role2], 'requireAll' => true];
        $this->assertEquals('has role', $this->renderView('role', $viewData));
    }

    /** @test */
    public function role_directive_returns_false_if_a_user_has_all_role_as_array_models()
    {
        $role1 = Role::create(['name' => 'role1']);
        $role2 = Role::create(['name' => 'role2']);

        $this->userModel->assignRole($role1);
        auth()->setUser($this->userModel);

        $viewData = ['role' => [$role1, $role2], 'requireAll' => true];
        $this->assertEquals('has role', $this->renderView('role', $viewData));
    }

    protected function renderView($view, $parameters)
    {
        Artisan::call('view:clear');
        if (is_string($view)) {
            $view = view($view)->with($parameters)->render();
        }
        return trim($view);
    }
}