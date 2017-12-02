<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Seeder\Seeder;

class RoleTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_assign_permission_to_role()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->assertTrue($this->userRoleModel->permissions->contains('id', $this->userPermissionModel->id));

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertTrue(in_array($this->userPermissionModel->name, $roles[$this->userRoleModel->name]));
        }
    }

    /** @test */
    public function it_has_permission()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->assertTrue($this->userRoleModel->hasPermission($this->userPermissionModel));
    }

    /** @test */
    public function it_can_unassign_permission()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->userRoleModel->revokePermission($this->userPermissionModel);
        $this->assertTrue($this->userRoleModel->permissions->contains('id', $this->userPermissionModel->id));

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertFalse(in_array($this->userPermissionModel->name, $roles[$this->userRoleModel->name]));
        }
    }

    /** @test */
    public function it_can_unassign_all_permissins()
    {
        $this->userRoleModel->revokeAllPermissions();
        $this->assertCount(0, $this->userRoleModel->permissions);

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertCount(0, $roles[$this->userRoleModel->name]);
        }
    }

    /** @test */
    public function it_can_assign_role_to_a_user()
    {
        $this->userModel->assignRole([$this->userRoleModel, $this->adminRoleModel]);
        $this->assertTrue($this->userModel->roles->contains('id', $this->userRoleModel->id));
    }

    /** @test */
    public function it_can_unassign_roles_from_a_user()
    {
        $this->userModel->revokeRole($this->userRoleModel);
        $this->assertFalse($this->userModel->roles->contains('id', $this->userRoleModel->id));
    }

    /** @test */
    public function it_can_unassign_all_roles_from_a_user()
    {
        $this->userModel->revokeAllRoles();
        $this->assertCount(0, $this->userModel->roles);
    }
}
