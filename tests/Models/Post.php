<?php

namespace PermissionsHandler\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['id', 'user_id'];
}
