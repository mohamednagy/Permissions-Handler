<?php

namespace PermissionsHandler\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(\PermissionsHandler\Models\Role::class);
    }
}
