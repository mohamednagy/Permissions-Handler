<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler\Models\Permission;
use PermissionsHandler\Models\Role;

class CommandTest extends TestCase
{
    /** @test */
    public function it_can_create_a_role()
    {
        \Artisan::call('permissions:add', ['--role' => 'addedRole']);

        $this->assertCount(1, Role::where('name', 'addedRole')->get());
    }

    /** @test */
    public function it_can_create_a_permission()
    {
        \Artisan::call('permissions:add', ['--permission' => 'addedPermission']);

        $this->assertCount(1, Permission::where('name', 'addedPermission')->get());
    }


    /** @test */
    public function it_can_assign_permission_to_role()
    {
        \Artisan::call('permissions:assign', ['--role' => 'testAdminRole', '--permission' => 'admin-permission']);

        $this->assertTrue($this->testAdminRole->permissions->contains('name', 'admin-permission'));
    }
}
