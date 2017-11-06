<?php

namespace PermissionsHandler;

trait CanDo {


    public function roles(){
        return $this->belongsToMany(\PermissionsHandler\Models\Role::class);
    }


    public function canDo(){
        
    }


}









?>