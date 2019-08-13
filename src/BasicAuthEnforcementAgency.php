<?php

namespace OwenMelbz\BasicAuthEnforcement;

use View;
use Closure;
use \Illuminate\Http\Request;

class BasicAuthEnforcementAgency {

    protected static $except = [];

    protected static $ipExclusions = [];

    protected $user;
    protected $password;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->shouldHandleRequest($request)) {
            $this->getAuthenticationHeaders();

            if (!$this->isAuthenticated()) {
                $this->offerAuthentication();
            }
        }

        return $next($request);
    }

    /**
     * Checks if the provided logins match the config
     *
     * @return bool
     */
    private function isAuthenticated()
    {
        return
            $this->user == config('basic_auth_enforcement.basic_auth_user') &&
            $this->password == config('basic_auth_enforcement.basic_auth_password');
    }

    /**
     * Launches a login prompt to provide details, and if they cancel it
     * it then spins up an error page.
     *
     * @return void
     */
    private function offerAuthentication()
    {
        header('WWW-Authenticate: Basic realm="' . config('basic_auth_enforcement.basic_auth_realm') . '"');
        header('HTTP/1.0 401 Unauthorized');
        echo View::make(config('basic_auth_enforcement.fail_template'))->render();
        exit;
    }

    /**
     * This tries to populate the user/password property
     * based off various headers which could contain the user credentials
     *
     * @return void
     */
    private function getAuthenticationHeaders()
    {
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $this->user = $_SERVER['PHP_AUTH_USER'];
            $this->password = $_SERVER['PHP_AUTH_PW'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            list($this->user, $this->password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            list($this->user, $this->password) = explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
        } elseif (isset($_SERVER['Authorization'])) {
            list($this->user, $this->password) = explode(':', base64_decode(substr($_SERVER['Authorization'], 6)));
        }
    }


    /**
     * Determine if the request should get handled
     *
     * @return bool
     */
    protected function shouldHandleRequest(Request $request)
    {

        if (config('basic_auth_enforcement.enforce_basic_auth') !== true) {
            return false;
        }

        if ($this->inExceptArray($request)) {
            return false;
        }

        if ($this->inIpExclusionArray($request->ip())) {
            return false;
        }

        return true;
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

    protected function inIpExclusionArray($ip)
    {
        return in_array($ip, self::getIpExclusions());
    }

    public static function setExceptions($except = [])
    {
        self::$except = $except;
    }

    public static function getExceptions()
    {
        return self::$except;
    }

    public static function setIpExclusions($exclusions = [])
    {
        self::$ipExclusions = $exclusions;
    }

    public static function getIpExclusions()
    {
        return self::$ipExclusions;
    }
}
