<?php

namespace Funtv\PHPRedis;

use Illuminate\Support\ServiceProvider;

class PHPRedisServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('phpredis', function ($app) {
            $app->configure('database');
            return new Database($app->config['database.phpredis']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['phpredis'];
    }
}