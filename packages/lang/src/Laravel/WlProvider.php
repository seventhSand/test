<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 1/24/2017
 * Time: 10:31 AM
 */

namespace Webarq\Lang\Laravel;



use Webarq\Lang\Wl;

use Illuminate\Support\ServiceProvider;

class WlProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('wl', function () {
            return new Wl();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('wl');
    }
}