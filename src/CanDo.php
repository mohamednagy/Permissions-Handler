<?php

namespace PermissionsHandler;

use PermissionsHandler;

trait CanDo {


    public function roles(){
        return $this->belongsToMany(\PermissionsHandler\Models\Role::class);
    }


    public function canDo($permissions){
        return PermissionsHandler::hasPermissions($permissions);
    }


}









?>