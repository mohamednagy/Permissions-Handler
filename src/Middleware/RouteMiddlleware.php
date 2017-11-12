<?php

namespace PermissionsHandler\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use PermissionsHandler;
use PermissionsHandler\Permissions;
use PermissionsHandler\Roles;

class RouteMiddlleware
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

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
        if (is_array($permissions)) {
            $canGo = with(new Permissions($permissions))->check(config('permissionsHandler.aggressiveMode'));
        }
        if (is_array($roles)) {
            $canGo = with(new Roles($roles))->check(config('permissionsHandler.aggressiveMode'));
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
