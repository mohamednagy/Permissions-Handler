<?php

namespace PermissionsHandler\Middleware;

use Closure;
use PermissionsHandler;
use PermissionsHandler\Roles;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param array                    $roles
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $roles, $requireAll = false)
    {
        if (PermissionsHandler::isExcludedRoute($request)) {
            return $next($request);
        }

        $roles = explode('|', $roles);
        $requireAll = (boolean) $requireAll;
        $canGo = with(new Roles($roles, $requireAll))->check();

        if (! $canGo) {
            $redirectTo = config('permissionsHandler.redirectUrl');
            if ($redirectTo) {
                return redirect($redirectTo);
            }

            return abort(403);
        }

        return $next($request);
    }
}
