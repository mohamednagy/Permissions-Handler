<?php

namespace PermissionsHandler\Models;

use Illuminate\Database\Eloquent\Model;
use PermissionsHandler\Seeder\Seeder;

class Permission extends Model
{
    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('permissionsHandler.tables.permissions'));
    }

    /**
     * The "booting" method of the model.
     */
    public static function boot()
    {
        if (config('permissionsHandler.seeder') == true) {
            self::created(function ($permission) {
                Seeder::seedPermission($permission);
            });
        }
    }

    public function roles()
    {
        $permissionsForeignKeyName = Inflector::singularize(config('permissionsHandler.tables.permissions')).'_id';
        $rolesForeignKeyName = Inflector::singularize(config('permissionsHandler.tables.roles')).'_id';

        return $this->belongsToMany(
            \PermissionsHandler\Models\Permission::class,
            config('permissionsHandler.tables.permission_role'),
            $rolesForeignKeyName,
            $permissionsForeignKeyName
        );
    }
}
