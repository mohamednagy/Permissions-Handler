<?php

namespace PermissionsHandler;

use PermissionsHandler\Annotations\Checkable;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Owns implements Checkable
{
    public $relation;

    public $attribute;

    public $key;


    public function check()
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        $request = app(\Illuminate\Http\Request::class);

        if ($this->key == null) {
            $this->key = $this->attribute;
        }

        $result = $user->{$this->relation}->contains($this->key, $request->{$this->attribute});
        
        return $result;
    }
}