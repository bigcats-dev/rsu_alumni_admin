<?php

namespace App\Http\Middleware;

use Closure;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,...$permissions)
    {
         /**
         * check for super administrator
         *
         *
         */
        if($request->user()->isSuperAdmin()) {
            return $next($request);
        }

        if($request->user()->hasPermission($permissions)) {
            return $next($request);
        }else {
            return redirect('/');
        }
    }
}
