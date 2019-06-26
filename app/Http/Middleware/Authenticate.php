<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * Authenticate constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request
     *
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // First, check if the access_token created by the password grant is valid
        if ($this->auth->guard($guard)->guest()) {

            // Then check, access_token created by the client_credentials grant is valid.
            // We need this checking because we could use either password grant or client_credentials grant.
            try {
                /* @var CheckClientCredentials $checkClientCredentials */
                $checkClientCredentials = app(CheckClientCredentials::class);
                $checkClientCredentials->handle($request, function () {
                    // Handle
                });
            } catch (\Exception $e) {
                return response()->json((['status' => 401, 'message' => 'Unauthorized']), 401);
            }
        }

        return $next($request);
    }
}
