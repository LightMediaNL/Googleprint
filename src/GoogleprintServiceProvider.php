<?php

namespace Lightmedia\Googleprint;

use Illuminate\Support\ServiceProvider;

class GoogleprintServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Googleprint', 'LightMedia\Googleprint\Googleprint' );
    }
}
