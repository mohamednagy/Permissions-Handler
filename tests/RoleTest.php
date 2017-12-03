<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler;
use PermissionsHandler\Models\Role;
use PermissionsHandler\Seeder\Seeder;
use PermissionsHandler\Models\Permission;

class RoleTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_assign_permission_model_to_role()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->assertTrue($this->userRoleModel->permissions->contains('id', $this->userPermissionModel->id));

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertTrue(in_array($this->userPermissionModel->name, $roles[$this->userRoleModel->name]));
        }
    }

    /** @test */
    public function it_can_assign_permission_by_name_to_role()
    {
        $permissionTest = Permission::create(['name' => 'permissionTest']);
        $this->userRoleModel->assignPermission('permissionTest');
        $this->assertTrue($this->userRoleModel->permissions->contains('name', 'permissionTest'));

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertTrue(in_array('permissionTest', $roles[$this->userRoleModel->name]));
        }
    }


    /** @test */
    public function it_can_assign_many_permissions_models_to_role_as_array()
    {
        $permissionTest1 = Permission::create(['name' => 'permissionTest1']);
        $permissionTest2 = Permission::create(['name' => 'permissionTest2']);

        $this->userRoleModel->assignPermission([$permissionTest1, $permissionTest2]);
        $this->assertArraySubset(
            [$permissionTest1->id, $permissionTest2->id],
            $this->userRoleModel->permissions->pluck('id')->toArray()  
        );

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertArraySubset(
                [$permissionTest1->name, $permissionTest2->name],
                $roles[$this->userRoleModel->name]
            );
        }
    }

    /** @test */
    public function it_can_assign_many_permissions_models_to_role_as_arguments()
    {
        $permissionTest1 = Permission::create(['name' => 'permissionTest1']);
        $permissionTest2 = Permission::create(['name' => 'permissionTest2']);

        $this->userRoleModel->assignPermission($permissionTest1, $permissionTest2);
        $this->assertArraySubset(
            [$permissionTest1->id, $permissionTest2->id],
            $this->userRoleModel->permissions->pluck('id')->toArray()  
        );

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertArraySubset(
                [$permissionTest1->name, $permissionTest2->name],
                $roles[$this->userRoleModel->name]
            );
        }
    }


    /** @test */
    public function it_can_assign_many_permissions_by_names_to_role_as_arguments()
    {
        $permissionTest3 = Permission::create(['name' => 'permissionTest3']);
        $permissionTest4 = Permission::create(['name' => 'permissionTest4']);
        
        $this->userRoleModel->assignPermission('permissionTest3', 'permissionTest4');
        $this->assertArraySubset(
            ['permissionTest3', 'permissionTest4'],
            $this->userRoleModel->permissions->pluck('name')->toArray()
        );

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertArraySubset(
                ['permissionTest3', 'permissionTest4'],
                $roles[$this->userRoleModel->name]
            );
        }
    }

    /** @test */
    public function it_can_assign_many_permissions_by_names_to_role_as_array()
    {
        $permissionTest5 = Permission::create(['name' => 'permissionTest5']);
        $permissionTest6 = Permission::create(['name' => 'permissionTest6']);
        
        $this->userRoleModel->assignPermission(['permissionTest5', 'permissionTest6']);
        $this->assertArraySubset(
            ['permissionTest5', 'permissionTest6'],
            $this->userRoleModel->permissions->pluck('name')->toArray()
        );

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertArraySubset(
                ['permissionTest5', 'permissionTest6'],
                $roles[$this->userRoleModel->name]
            );
        }
    }

    /** @test */
    public function it_has_permission_model()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel);
        $this->assertTrue($this->userRoleModel->hasPermission($this->userPermissionModel));
    }

    /** @test */
    public function it_has_permission_name()
    {
        $this->userRoleModel->assignPermission($this->userPermissionModel->name);
        $this->assertTrue($this->userRoleModel->hasPermission($this->userPermissionModel->name));
    }
    

    /** @test */
    public function it_has_permission_at_least_one_permission_names()
    {
        $permissionTest7 = Permission::create(['name' => 'permissionTest7']);
        $permissionTest8 = Permission::create(['name' => 'permissionTest8']);

        $this->userRoleModel->assignPermission($permissionTest7, $permissionTest8);
        $this->assertTrue($this->userRoleModel->hasPermission(['permissionTest7', 'permissionTest8']));
    }

    /** @test */
    public function it_has_permission_at_least_one_permission_models()
    {
        $permissionTest7 = Permission::create(['name' => 'permissionTest7']);
        $permissionTest8 = Permission::create(['name' => 'permissionTest8']);

        $this->userRoleModel->assignPermission($permissionTest7, $permissionTest8);
        $this->assertTrue($this->userRoleModel->hasPermission([$permissionTest7, $permissionTest8]));
    }

    /** @test */
    public function it_has_all_permissions()
    {
        $permissionTest7 = Permission::create(['name' => 'permissionTest7']);
        $permissionTest8 = Permission::create(['name' => 'permissionTest8']);

        $this->userRoleModel->assignPermission($permissionTest7);
        $this->assertFalse($this->userRoleModel->hasPermission([$permissionTest7, $permissionTest8], true));
    }


    /** @test */
    public function it_can_revoke_permission_model()
    {
        $this->userRoleModel->revokePermission($this->userPermissionModel);
        $this->assertFalse($this->userRoleModel->permissions->contains('id', $this->userPermissionModel->id));

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $this->assertFalse(in_array($this->userPermissionModel->name, $roles[$this->userRoleModel->name]));
        }
    }
    

    /** @test */
    public function it_can_revoke_permission_models_as_array()
    {
        $permissionTest7 = Permission::create(['name' => 'permissionTest7']);
        $permissionTest8 = Permission::create(['name' => 'permissionTest8']);

        $this->userRoleModel->revokePermission([$permissionTest7, $permissionTest8]);

        $permissionIds = $this->userRoleModel->permissions->pluck('id')->toArray();
        $this->assertCount(0, array_intersect([$permissionTest7->id, $permissionTest8->id], $permissionIds));

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $permissionNames = $this->userRoleModel->permissions->pluck('name')->toArray();
            $this->assertCount(0, array_intersect([$permissionTest7->name, $permissionTest8->name], $permissionNames));
        }
    }

    /** @test */
    public function it_can_revoke_permission_models_as_arguments()
    {
        $permissionTest7 = Permission::create(['name' => 'permissionTest7']);
        $permissionTest8 = Permission::create(['name' => 'permissionTest8']);

        $this->userRoleModel->revokePermission($permissionTest7, $permissionTest8);

        $permissionIds = $this->userRoleModel->permissions->pluck('id')->toArray();
        $this->assertCount(0, array_intersect([$permissionTest7->id, $permissionTest8->id], $permissionIds));

        if (config('permissionsHandler.seeder') == true) {
            $roles = Seeder::getFileContent('role-permissions.json');
            $permissionNames = $this->userRoleModel->permissions->pluck('name')->toArray();
            $this->assertCount(0, array_intersect([$permissionTest7->name, $permissionTest8->name], $permissionNames));
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
}
