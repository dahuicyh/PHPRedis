<?php

namespace Dahuicyh\PHPRedis\Cache;

use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        app('cache')->extend('phpredis', function ($app) {
            return app('cache')->repository(new PHPRedisStore($app->make('phpredis'), $app->config['cache.prefix'], $app->config['cache.stores.phpredis.connection']));
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
