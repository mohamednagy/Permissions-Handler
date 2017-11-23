<?php

namespace PermissionsHandler\Models;

use Illuminate\Database\Eloquent\Model;
use PermissionsHandler\Traits\RoleTrait;
use PermissionsHandler\Seeder\Seeder;

class Role extends Model
{
    use RoleTrait;
    
    protected $table = 'roles';

    protected $guarded = [];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        self::created(function ($role) {
            Seeder::seedRole($role);
        });
    }

    public function permissions()
    {
        return $this->belongsToMany(\PermissionsHandler\Models\Permission::class);
    }

    public function users()
    {
        return $this->belongsToMany(config('permissionsHandler.user'), 'role_user');
    }
}
