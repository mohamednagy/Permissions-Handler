<?php

namespace PermissionsHandler\Middleware;

use Closure;
use PermissionsHandler;

class MethodMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (PermissionsHandler::isExcludedRoute($request)) {
            return $next($request);
        }

        if (!PermissionsHandler::canGo($request)) {
            $redirectTo = config('permissionsHandler.redirectUrl');
            if ($redirectTo) {
                return redirect($redirectTo);
            }

            return abort(403);
        }

        return $next($request);
    }
}
