<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Seeder\Seeder;
use PermissionsHandler\Tests\Models\User;

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
        $this->assertTrue($this->userRoleModel->hasPermission($this->userPermissionModel));
    }


    /** @test */
    public function it_can_unassign_permission()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->userRoleModel->unAssignPermission($this->userPermissionModel);
        $this->assertTrue($this->userRoleModel->permissions->contains('id', $this->userPermissionModel->id));

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertFalse(in_array($this->userPermissionModel->name, $roles[$this->userRoleModel->name]));
        }
    }

    /** @test */
    public function it_can_unassign_all_permissins()
    {
        $this->userRoleModel->unAssignAllPermissions();
        $this->assertCount(0, $this->userRoleModel->permissions);

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertCount(0, $roles[$this->userRoleModel->name]);
        }
    }
}
