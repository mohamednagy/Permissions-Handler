<?php

namespace PermissionsHandler\Middleware;

use Closure;
use PermissionsHandler;
use Illuminate\Support\Facades\Auth;

class PermissionsHandlerMiddleware
{

    protected $permissionsHandler;


    function __construct(PermissionsHandler $permissionsHandler)
    {
        $this->permissionsHandler = $permissionsHandler;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$this->permissionsHandler->can()){
            $redirectTo = config('permissionsHandler.redirectUrl');
            if($redirectTo){
                return redirect($redirectTo);
            }
            return abort(403);
        }
        return $next($request);
    }
}
