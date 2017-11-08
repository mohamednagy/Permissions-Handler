<?php namespace PermissionsHandler\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

	protected $table = "roles";

	protected $guarded = [];

	function permissions()
	{
		return $this->belongsToMany(\PermissionsHandler\Models\Permission::class);
	}


	function users()
	{
		return $this->belongsToMany(cofig('permissionsHandler.user'), 'role_user');
	}



}
