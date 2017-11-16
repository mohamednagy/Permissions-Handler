<?php

namespace PermissionsHandler\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class RoleTest extends Model
{
    protected $table = 'roles';

    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(\PermissionsHandler\Models\Permission::class);
    }

    public function users()
    {
        return $this->belongsToMany(cofig('permissionsHandler.user'), 'role_user');
    }
}
