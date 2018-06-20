<?php

namespace RichanFongdasen\Varnishable;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            realpath(__DIR__.'/../config/varnishable.php') => config_path('varnishable.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/varnishable.php'), 'varnishable');

        $this->app->singleton(VarnishableService::class, function () {
            return new VarnishableService(new Client());
        });
    }
}
