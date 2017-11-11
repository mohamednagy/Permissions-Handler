<?php

namespace PermissionsHandler\Middleware;

use Closure;
use PermissionsHandler;
use Illuminate\Contracts\Auth\Guard;

class PermissionsHandlerMiddleware
{

    protected $auth;

    public function __construct(Guard $auth){
        $this->auth = $auth;
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
        if(!PermissionsHandler::canGo($request)){
            $redirectTo = config('permissionsHandler.redirectUrl');
            if($redirectTo){
                return redirect($redirectTo);
            }
            return abort(403);
        }
        return $next($request);
    }
}
