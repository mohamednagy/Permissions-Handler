<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler;
use PermissionsHandler\Tests\Models\User;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Exceptions\RoleAlreadyExists;

class RoleTest extends TestCase
{
    /** @test */
    public function it_can_assign_permission_to_role()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->assertTrue($this->userRoleModel->permissions->contains('id', $this->userPermissionModel->id));
    }

    /** @test */
    public function it_has_permission()
    {
        $this->assertTrue($this->userRoleModel->hasPermission($this->userPermissionModel));
    }


    // /** @test */
    // public function it_can_unassign_permission()
    // {
    //     $this->userRoleModel->unassignPermission($this->userPermissionModel);
    //     $this->assertFalse($this->userRoleModel->permissions->contains('id', $this->userPermissionModel->id));
    // }

    /** @test */
    public function it_can_unassign_all_permissins()
    {
        $this->userRoleModel->unAssignAllPermissions();
        $this->assertCount(0, $this->userRoleModel->permissions);
    }
}
