<?php

namespace Funtv\PHPRedis\Queue;

use Illuminate\Support\Arr;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Funtv\PHPRedis\Database;
use TargetLiu\PHPRedis\Queue\PHPRedisQueue;

class PHPRedisConnector implements ConnectorInterface
{
    /**
     * The Redis database instance.
     *
     * @var \Redis
     */
    protected $redis;

    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection;


    /**
     * Create a new Redis queue connector instance.
     *
     * @param Database|\Redis $redis
     * @param null $connection
     */
    public function __construct(Database $redis, $connection = null)
    {
        $this->redis = $redis;
        $this->connection = $connection;
    }

    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $queue = new PHPRedisQueue(
            $this->redis, $config['queue'], Arr::get($config, 'connection', $this->connection)
        );

        $queue->setExpire(Arr::get($config, 'expire', 60));

        return $queue;
    }
}
