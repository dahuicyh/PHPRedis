### Basic

- Add `$app->register(Dahuicyh\PHPRedis\PHPRedisServiceProvider::class);` in *bootstrap/app.php*

### Cache Driver

- Add `$app->register(Dahuicyh\PHPRedis\Cache\CacheServiceProvider::class);` in *bootstrap/app.php* in order to use PhpRedis with Lumen cache
- Add 

```
'phpredis' => [
    'driver' => 'phpredis',
    'connection' => 'default',
],
```

to **stores** in *config/cache.php* or *vendor/larvel/lumen-framework/config/app.php* in order to use PhpRedis with Lumen cache

- Set `CACHE_DRIVER=phpredis` in *.env*


### Queue Driver

- Add `$app->register(Dahuicyh\PHPRedis\Queue\QueueServiceProvider::class);` in *bootstrap/app.php* in order to use PhpRedis with Lumen queue
- Add 

```
'phpredis' => [
    'driver'     => 'phpredis',
	'connection' => 'default',
	'queue'      => 'default',
	'expire'     => 60,
],
```

to **connections** in *config/queue.php* or *vendor/larvel/lumen-framework/config/queue.php* in order to use PhpRedis with Lumen queue

- Set `QUEUE_DRIVER=phpredis` in *.env*

## About the author
dahuicyh@qq.com