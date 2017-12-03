<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Seeder\Seeder;
use PermissionsHandler\Models\Permission;

class UserTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    
    public function it_can_assign_role_model_to_user()
    {
        $this->userModel->assignRole($this->userRoleModel);
        $this->assertTrue($this->userModel->roles->contains('id', $this->userRoleModel->id));
    }

    /** @test */
    public function it_can_assign_role_models_to_user_as_array()
    {
        $role1 = Role::create(['name' => 'role1']);
        $role2 = Role::create(['name' => 'role2']);

        $this->userModel->assignRole([$role1, $role2]);

        $this->assertArraySubset(
            [$role1->id, $role2->id],
            $this->userModel->roles->pluck('id')->toArray()
        );
    }

    /** @test */
    public function it_can_assign_role_models_to_user_as_arguments()
    {
        $role1 = Role::create(['name' => 'role1']);
        $role2 = Role::create(['name' => 'role2']);

        $this->userModel->assignRole($role1, $role2);

        $this->assertArraySubset(
            [$role1->id, $role2->id],
            $this->userModel->roles->pluck('id')->toArray()
        );
    }

    /** @test */
    public function it_can_assign_role_to_user_by_name()
    {
        $role1 = Role::create(['name' => 'role1']);

        $this->userModel->assignRole('role1');

        $this->assertTrue($this->userModel->roles->contains('name', $role1->name));
    }

    /** @test */
    public function it_can_assign_roles_to_user_by_name_as_array()
    {
        $role1 = Role::create(['name' => 'role1']);
        $role2 = Role::create(['name' => 'role2']);

        $this->userModel->assignRole(['role1', 'role2']);

        $this->assertArraySubset(
            [$role1->name, $role2->name],
            $this->userModel->roles->pluck('name')->toArray()
        );
    }

    /** @test */
    public function it_can_assign_roles_to_user_by_name_as_arguments()
    {
        $role1 = Role::create(['name' => 'role1']);
        $role2 = Role::create(['name' => 'role2']);

        $this->userModel->assignRole('role1', 'role2');

        $this->assertArraySubset(
            [$role1->name, $role2->name],
            $this->userModel->roles->pluck('name')->toArray()
        );
    }

    /** @test */
    public function a_user_has_permission_model()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->userModel->assignRole($this->userRoleModel);

        $this->assertTrue($this->userRoleModel->hasPermission($this->userPermissionModel));
    }

    /** @test */
    public function a_user_has_at_least_permissions_models_as_array()
    {
        $role = Role::create(['name' => 'testRole']);
        $permission1 = Permission::create(['name' => 'permission1']);
        $permission2 = Permission::create(['name' => 'permission2']);

        $role->assignPermission($permission1, $permission2);
        $this->userModel->assignRole($role);
        
        $this->assertTrue($this->userModel->hasPermission([$permission1, $permission2]));
    }

    /** @test */
    public function a_user_permission_by_name()
    {
        $role = Role::create(['name' => 'testRole']);
        $permission1 = Permission::create(['name' => 'permission1']);

        $role->assignPermission($permission1);
        $this->userModel->assignRole($role);
        
        $this->assertTrue($this->userModel->hasPermission('permission1'));
    }

    /** @test */
    public function a_user_permissions_by_name_as_array()
    {
        $role = Role::create(['name' => 'testRole']);
        $permission1 = Permission::create(['name' => 'permission1']);
        $permission2 = Permission::create(['name' => 'permission2']);

        $role->assignPermission($permission1, $permission2);
        $this->userModel->assignRole($role);
        
        $this->assertTrue($this->userModel->hasPermission(['permission1', 'permission2']));
    }

    /** @test */
    public function a_user_must_has_all_permissions_by_name_as_array()
    {
        $role = Role::create(['name' => 'testRole']);
        $permission1 = Permission::create(['name' => 'permission1']);
        $permission2 = Permission::create(['name' => 'permission2']);

        $role->assignPermission($permission1);
        $this->userModel->assignRole($role);
        
        $this->assertFalse($this->userModel->hasPermission(['permission1', 'permission2'], true));
    }



}