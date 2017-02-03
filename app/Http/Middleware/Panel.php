<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/30/2017
 * Time: 1:33 PM
 */

namespace App\Http\Middleware;


use Closure;
use Illuminate\Contracts\Auth\Guard;
use URL;

class Panel
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()
                && 0 !== strpos(\Request::path(),
                        config('webarq.system.panel-url-prefix', 'admin-cp') . '/system/admins/auth/login')
        ) {
            return redirect(config('webarq.system.panel-url-prefix', 'admin-cp') . '/system/admins/auth/login');
        }

        /**
         * @todo Controller / Action filtering through this, is it possible ?
         */

        return $next($request);
    }
}