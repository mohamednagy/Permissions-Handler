<?php

namespace PermissionsHandler\Models;

use PermissionsHandler\Seeder\Seeder;
use Illuminate\Database\Eloquent\Model;
use PermissionsHandler\Traits\RoleTrait;
use Doctrine\Common\Inflector\Inflector;

class Role extends Model
{
    use RoleTrait;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('permissionsHandler.tables.roles'));
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        if (config('permissionsHandler.seeder') == true) {
            self::created(function ($role) {
                Seeder::seedRole($role);
            });
        }
    }

    public function permissions()
    {
        $permissionsForeignKeyName = Inflector::singularize(config('permissionsHandler.tables.permissions')).'_id';
        $rolesForeignKeyName = Inflector::singularize(config('permissionsHandler.tables.roles')).'_id';

        return $this->belongsToMany(
            \PermissionsHandler\Models\Permission::class,
            config('permissionsHandler.tables.permission_role'),
            $permissionsForeignKeyName,
            $rolesForeignKeyName
        );
    }
}
