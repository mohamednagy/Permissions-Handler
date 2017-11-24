<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler\Models\Permission;
use PermissionsHandler\Models\Role;

class CommandTest extends TestCase
{
    /** @test */
    public function it_can_create_a_role()
    {
        \Artisan::call('permissions:add', ['--role' => 'fromCommandRole']);

        $this->assertCount(1, Role::where('name', 'fromCommandRole')->get());
    }

    /** @test */
    public function it_can_create_a_permission()
    {
        \Artisan::call('permissions:add', ['--permission' => 'fromCommandPermission']);

        $this->assertCount(1, Permission::where('name', 'fromCommandPermission')->get());
    }


    /** @test */
    public function it_can_assign_permission_to_role()
    {
        \Artisan::call('permissions:assign', ['--role' => parent::USER_ROLE, '--permission' => parent::USER_PERMISSION]);
        $this->assertTrue($this->userRoleModel->permissions->contains('name', parent::USER_PERMISSION));
    }


    /** @test */
    public function it_can_assign_role_to_user()
    {
        \Artisan::call('permissions:assign', ['--role' => parent::USER_ROLE, '--user-id' => $this->userModel->id]);
        $this->assertTrue($this->userModel->roles->contains('name', parent::USER_ROLE));
    }
}
