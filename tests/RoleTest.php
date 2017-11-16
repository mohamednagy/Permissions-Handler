<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler;
use PermissionsHandler\Tests\Models\User;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Exceptions\RoleAlreadyExists;

class RoleTest extends TestCase
{
    /** @test */
    public function it_has_user_models_of_the_right_class()
    {
        PermissionsHandler::assignRoleToUser($this->testAdminRole, $this->testAdmin);
        PermissionsHandler::assignRoleToUser($this->testUserRole, $this->testUser);

        $this->assertCount(1, $this->testUserRole->users);
        $this->assertTrue($this->testUserRole->users->first()->is($this->testUser));
        $this->assertInstanceOf(User::class, $this->testUserRole->users->first());
    }

    /** @test */
    public function it_throws_an_exception_when_the_role_already_exists()
    {
        $this->expectException(RoleAlreadyExists::class);
        app(Role::class)->create(['name' => 'test-role']);
        app(Role::class)->create(['name' => 'test-role']);
    }

    /** @test */
    public function it_can_be_given_a_permission()
    {
        PermissionsHandler::assignPermissionToRole($this->testUserPermission, $this->testUserRole);
        $this->assertTrue($this->testUserRole->permissions->contains('name', 'user-permission'));
    }
}
