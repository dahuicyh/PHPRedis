<?php

namespace Funtv\PHPRedis\Cache;

use Illuminate\Contracts\Cache\Store;

class PHPRedisStore implements Store
{

    /**
     * The Redis database connection.
     *
     * @var \Redis
     */
    protected $redis;

    /**
     * A string that should be prepended to keys.
     *
     * @var string
     */
    protected $prefix;

    /**
     * The Redis connection that should be used.
     *
     * @var string
     */
    protected $connection;

    /**
     * Create a new Redis store.
     *
     * @param  \Redis $redis
     * @param  string $prefix
     * @param string $connection
     */
    public function __construct($redis, $prefix = '', $connection = 'default')
    {
        $this->redis = $redis;
        $this->setPrefix($prefix);
        $this->connection = $connection;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function get($key)
    {
        if ($value = $this->connectionRead()->get($this->prefix . $key)) {
            return is_numeric($value) ? $value : unserialize($value);
        }
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array  $keys
     * @return array
     */
    public function many(array $keys)
    {
        $return = [];

        $prefixedKeys = array_map(function ($key) {
            return $this->prefix . $key;
        }, $keys);

        $values = $this->connectionRead()->mGet($prefixedKeys);

        foreach ($values as $index => $value) {
            $return[$keys[$index]] = is_numeric($value) ? $value : unserialize($value);
        }

        return $return;
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  int     $minutes
     * @return void
     */
    public function put($key, $value, $minutes)
    {
        $value = is_numeric($value) ? $value : serialize($value);

        $this->connection()->set($this->prefix . $key, $value, (int) max(1, $minutes * 60));
    }

    /**
     * Store multiple items in the cache for a given number of minutes.
     *
     * @param  array  $values
     * @param  int  $minutes
     * @return void
     */
    public function putMany(array $values, $minutes)
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $minutes);
        }
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->connection()->incrBy($this->prefix . $key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->connection()->decrBy($this->prefix . $key, $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function forever($key, $value)
    {
        $value = is_numeric($value) ? $value : serialize($value);

        $this->connection()->set($this->prefix . $key, $value);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        return (bool) $this->connection()->delete($this->prefix . $key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function flush()
    {
        $this->connection()->flushDb();
    }

    /**
     * 写主库
     * Get the Redis connection instance.
     *
     * @return \Redis
     */
    public function connection()
    {
        return $this->redis->connection('default');
    }

    /**
     * 读缓存时候用
     * Get the Redis connection instance.
     *
     * @return \Redis
     */
    public function connectionRead()
    {
        //从库随机选取一台
        $clients = $this->redis->getArrClient();
        $num = array_rand($clients);
        return $this->redis->connection($clients[$num]);
    }

    /**
     * Set the connection name to be used.
     *
     * @param  string  $connection
     * @return void
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the Redis database instance.
     *
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the cache key prefix.
     *
     * @param  string  $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = !empty($prefix) ? $prefix . ':' : '';
    }
}
