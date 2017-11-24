<?php

namespace PermissionsHandler\Tests\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use PermissionsHandler\Traits\UserTrait;
use PermissionsHandler\Tests\Models\Post;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Authorizable, Authenticatable, UserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'name', 'password'];
    public $timestamps = false;
    protected $table = 'users';


    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}