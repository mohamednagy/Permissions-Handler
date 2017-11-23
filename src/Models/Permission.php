<?php

namespace PermissionsHandler\Models;

use Illuminate\Database\Eloquent\Model;
use PermissionsHandler\Seeder\Seeder;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $guarded = [];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        self::created(function ($permission) {
            Seeder::seedPermission($permission);
        });
    }

    public function roles()
    {
        return $this->belongsToMany(\PermissionsHandler\Models\Role::class);
    }
}
