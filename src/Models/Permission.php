<?php

namespace PermissionsHandler\Models;

use PermissionsHandler\Seeder\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Doctrine\Common\Inflector\Inflector;
use PermissionsHandler\Exceptions\PermissionNotFound;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function roles(): BelongsToMany
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

    public static function getByName(string $name): self
    {
        $permission = Cache::remember(
            'permissionsHandler.permissions.'.$name,
            config('permissionsHandler.cacheExpiration'),
            function () use ($name) {
                return self::where('name', $name)->first();
            }
        );

        if (! $permission) {
            throw new PermissionNotFound("Permission with name $name doesn't exists");
        }

        return $permission;
    }
}
