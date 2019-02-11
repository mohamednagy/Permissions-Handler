<?php

namespace PermissionsHandler\Models;

use PermissionsHandler\Seeder\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use PermissionsHandler\Traits\RoleTrait;
use Doctrine\Common\Inflector\Inflector;
use PermissionsHandler\Exceptions\RoleNotFound;

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
        parent::boot();
        
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
            $rolesForeignKeyName,
            $permissionsForeignKeyName
        );
    }

    public static function getByName(string $name): self
    {
        $role = Cache::remember(
            'permissionsHandler.roles.'.$name,
            config('permissionsHandler.cacheExpiration'),
            function () use ($name) {
                return self::where('name', $name)->first();
            }
        );
        if (! $role) {
            throw new RoleNotFound("Role with name $name doesn't exists");
        }

        return $role;
    }
}
