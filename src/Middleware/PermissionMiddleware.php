<?php

namespace PermissionsHandler\Middleware;

use Closure;
use PermissionsHandler;
use PermissionsHandler\Permissions;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param array                    $permissions
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions, $requireAll = false)
    {
        if (PermissionsHandler::isExcludedRoute($request)) {
            return $next($request);
        }

        $permissions = explode('|', $permissions);
        $requireAll = (boolean) $requireAll;
        $canGo = with(new Permissions($permissions, $requireAll))->check();

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
