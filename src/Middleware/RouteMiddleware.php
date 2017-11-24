<?php

namespace PermissionsHandler\Middleware;

use Closure;
use PermissionsHandler;
use PermissionsHandler\Roles;
use PermissionsHandler\Permissions;

class RouteMiddleware
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
    public function handle($request, Closure $next, ...$rolesAndPermissions)
    {
        if (PermissionsHandler::isExcludedRoute($request)) {
            return $next($request);
        }

        $user = auth()->user();
        $permissions = null;
        $roles = null;
        $canGo = false;
        foreach ($rolesAndPermissions as $item) {
            $arr = explode('@', $item);
            if ($arr[0] == 'permissions') {
                $permissions = explode(';', $arr[1]);
            }
            if ($arr[0] == 'roles') {
                $roles = explode(';', $arr[1]);
            }
        }

        if (config('aggressiveMode') == true && empty($roles) && empty($permissions)) {
            $canGo = false;
        } elseif (config('aggressiveMode') == false && empty($roles) && empty($permissions)) {
            $canGo = true;
        }

        if ($user && is_array($permissions)) {
            $canGo = $user->hasPermission($permissions);
        }
        if ($user && is_array($roles)) {
            $canGo = $user->hasRole($roles);
        }
        if (!$canGo) {
            $redirectTo = config('permissionsHandler.redirectUrl');
            if ($redirectTo) {
                return redirect($redirectTo);
            }

            return abort(403);
        }

        return $next($request);
    }
}
