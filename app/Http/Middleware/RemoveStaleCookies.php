<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class RemoveStaleCookies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            $this->killDeadCookies($request);
        }

        return $next($request);
    }

    private function killDeadCookies($request)
    {
        if (!empty($request->cookie())) {
            foreach ($request->cookie() as $key => $cookie) {
                if (strpos($key, 'remember_') !== false) {
                    Cookie::queue($key, null, -9999);
                }
            }
        }
    }
}
