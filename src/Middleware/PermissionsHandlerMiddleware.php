<?php

namespace PermissionsHandler\Middleware;

use Closure;
use PermissionsHandler;

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
        $user = \App\User::find(2);
        \Auth::login($user);
        // if route has permission
        $user = \Auth::user();
        if(config('permissionsHandler.aggressiveMode')){
            if(!$user){
              return redirect(config('permissionsHandler.redirectUrl'));
            }
            $this->permissionsHandler->setUser($user);
        }
        if(!$this->permissionsHandler->can()){
          return redirect(config('permissionsHandler.redirectUrl'));
        }
        $response = $next($request);
        $viewClass = 'Illuminate\View\View';
        $responseClass = "Illuminate\Http\Response";
        if ($response instanceof $responseClass) {
            $response->setContent($this->permissionsHandler->parseView($response->getOriginalContent(), $user));
        } elseif ($response instanceof $viewClass || is_string($response)) {
            $response = $this->permissionsHandler->parseView($response);
        }
        return $response;
    }
}
