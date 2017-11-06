<?php namespace PermissionsHandler\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

	protected $table = "permissions";

	protected $guarded = [];


	function roles(){
		return $this->belongsToMany(\PermissionsHandler\Models\Role::class);
	}
}
