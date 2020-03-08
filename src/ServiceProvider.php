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
    public function boot(): void
    {
        $this->publishes([
            realpath(dirname(__DIR__).'/config/varnishable.php') => config_path('varnishable.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $configPath = realpath(dirname(__DIR__).'/config/varnishable.php');

        if ($configPath !== false) {
            $this->mergeConfigFrom($configPath, 'varnishable');
        }

        $this->app->singleton(VarnishableService::class, function () {
            return new VarnishableService(new Client());
        });

        $this->app->singleton(VarnishableObserver::class, function () {
            return new VarnishableObserver();
        });
    }
}
