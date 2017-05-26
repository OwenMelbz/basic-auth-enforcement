<?php

namespace OwenMelbz\BasicAuthEnforcement;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider for BasicAuthEnforcement
 *
 * @author: Owen Melbourne
 */
class BasicAuthEnforcementServiceProvider extends ServiceProvider {

    /**
     * This will be used to register config & view in
     * your package namespace.
     *
     * --> Replace with your package name <--
     */
    protected $packageName = 'basic_auth_enforcement';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the config
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path($this->packageName.'.php'),
        ], 'config');

        if (config('basic_auth_enforcement.enforce_basic_auth') === true) {

            BasicAuthEnforcementAgency::setExceptions(
                config('basic_auth_enforcement.except')
            );

            BasicAuthEnforcementAgency::setIpExclusions(
                config('basic_auth_enforcement.exclude_ips')
            );

            $this->app->make('Illuminate\Contracts\Http\Kernel')->prependMiddleware(
                BasicAuthEnforcementAgency::class
            );
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/config.php', $this->packageName);
    }

}
