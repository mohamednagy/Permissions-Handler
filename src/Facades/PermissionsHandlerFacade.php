<?php

namespace PermissionsHandler\Facades;

use Illuminate\Support\Facades\Facade;

class PermissionsHandlerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'permissionsHandler';
    }
}
