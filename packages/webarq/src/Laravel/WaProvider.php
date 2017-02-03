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


use File;
use Illuminate\Support\ServiceProvider;
use Webarq\Wa;
use Request;


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

        view()->composer('*', function () {
            $id = (\Auth::id());
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