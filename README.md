# Laravel 5+ Basic Auth Enforcement

An automatic piece of middleware for Laravel 5+, will prompt users accessing your code base (static assets are excempt) to enter a username/password before seeing your application. Very useful for things like staging environments.


## Usage

1. Install via composer `composer require owenmelbz/basic-auth-enforcement`

2. Register the service provider - typically done inside the `app.php` providers array e.g `OwenMelbz\BasicAuthEnforcement\BasicAuthEnforcementServiceProvider::class`

3. Add `ENFORCE_BASIC_AUTH=true` to your application environment config e.g `.env`

4. Add your username `BASIC_AUTH_USER=username`

5. Add your password `BASIC_AUTH_PASSWORD=password`

6. Enjoy your stress free environment agnostic basic auth.

## Configuration

You can public the config using `php artisan vendor:publish --provider="OwenMelbz\BasicAuthEnforcement\BasicAuthEnforcementServiceProvider"` which will allow you to exclude IPs from the checks as well as custom endpoints, which is useful for payment gateway ping backs etc.


## Why?

Too often we've wasted time configuring password protection, with proxy systems like CloudFlare, with apache development machines and nginx production, this removes all the headache and can simply be turned off and on at a whim.
