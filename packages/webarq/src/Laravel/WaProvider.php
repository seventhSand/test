<?php
/**
 * Created by PhpStorm
 * Date: 24/10/2016
 * Time: 13:37
 * Author: Daniel Simangunsong
 *
 * Calm seas, never make skill full sailors
 */

namespace Webarq\Laravel;


use Auth;
use File;
use Illuminate\Hashing\BcryptHasher;
use Request;
use Webarq\Wa;

use Illuminate\Support\ServiceProvider;

class WaProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
// Load laravel extension
        if ([] !== ($scan = File::glob(__DIR__ . DIRECTORY_SEPARATOR . 'Extend' . DIRECTORY_SEPARATOR . '*.php'))) {
            foreach ($scan as $file) {
                app('Webarq\Laravel\Extend\\' . File::name($file));
            }
        }
        $this->addAuthProvider();
    }

    private function addAuthProvider()
    {
// Get class manager
        $manager = 0 === strpos(Request::path(), config('webarq.system.panel-url-prefix'))
                ? 'Webarq\Manager\AdminManager'
                : 'Webarq\Manager\MemberManager';
// Enable watchdog provider in to authentication
        Auth::provider('watchdog', function () use ($manager) {
//            dd(\Request::path());
// Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new WatchdogProvider(new BcryptHasher(), new $manager);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->registerWEBARQ();
//        $this->registerThemes();
    }

    /**
     * Register the Wa instance.
     *
     * @return void
     */
    protected function registerWEBARQ()
    {
        $this->app->singleton('wa', function ($app) {
            return new Wa($app);
        });
    }

//    /**
//     * Register the Themes instance.
//     *
//     * @return void
//     */
//    protected function registerThemes()
//    {
//        $this->app->singleton('themes', function ($app) {
//            return new Themes($app);
//        });
//    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('wa'/*, 'themes'*/);
    }
}