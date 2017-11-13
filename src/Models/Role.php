<?php

namespace PermissionsHandler\Models;

use Illuminate\Database\Eloquent\Model;
use PermissionsHandler\Traits\RoleTrait;

class Role extends Model
{
    use RoleTrait;
    
    protected $table = 'roles';

    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(\PermissionsHandler\Models\Permission::class);
    }

    public function users()
    {
        return $this->belongsToMany(config('permissionsHandler.user'), 'role_user');
    }
}
