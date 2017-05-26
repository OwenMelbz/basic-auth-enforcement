<?php

namespace OwenMelbz\BasicAuthEnforcement;

use Closure;

class BasicAuthEnforcementAgency {

    private static $except = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach (self::getExceptions() as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    public static function setExceptions($except = [])
    {
        self::$except = $except;
    }

    public static function getExceptions()
    {
        return self::$except;
    }
}
