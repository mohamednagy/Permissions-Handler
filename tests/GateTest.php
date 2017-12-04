<?php

namespace PermissionsHandler\Tests;

use PermissionsHandler\Models\Permission;

class GateTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_determine_if_a_user_does_not_have_a_permission()
    {
        $permission = Permission::create(['name' => 'add-post']);
        
        $this->assertFalse($this->userModel->can('add-post'));
    }
}