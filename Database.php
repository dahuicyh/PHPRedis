<?php
namespace Funtv\PHPRedis;

use Illuminate\Support\Arr;

class Database
{
    /**
     * The host address of the database.
     *
     * @var array
     */
    protected $clients;

    /**
     * The current database.
     *
     * @var array
     */
    protected $database;

    /**
     * Create a new Redis connection instance.
     * @param  array $servers
     */
    public function __construct(array $servers = [])
    {
        Arr::forget($servers, 'cluster');
        Arr::forget($servers, 'options');
        $this->clients = $this->createClient($servers);
        $this->database = $this->saveDatabase($servers);
    }

    /**
     * Save database
     *
     * @param  array $servers
     * @return array
     * @internal param array $options
     */
    protected function saveDatabase(array $servers)
    {
        $database = [];
        foreach ($servers as $key => $server) {
            $database[$key] = $server['database'];
        }

        return $database;
    }

    /**
     * Create an array of connection clients.
     *
     * @param  array $servers
     * @return array
     * @internal param array $options
     */
    protected function createClient(array $servers)
    {
        ini_set('default_socket_timeout', -1);
        $clients = [];
        foreach ($servers as $key => $server) {
            $clients[$key] = new \Redis();
            $clients[$key]->pconnect($server['host'], $server['port'], 0);
            if (!empty($server['password'])) {
                $clients[$key]->auth($server['password']);
            }
            if (!empty($server['database'])) {
                $clients[$key]->select($server['database']);
            }
        }
        return $clients;
    }

    /**
     * Get a specific Redis connection instance.
     *
     * @param  string  $name
     * @return \Redis
     */
    public function connection($name = 'default')
    {
        $connection = Arr::get($this->clients, $name ?: 'default');
        return $connection;
    }

    /**
     * 获取redis客户端连接数组
     * Get a specific Redis connection instance.
     * @return \Redis
     * @internal param string $name
     */
    public function getArrClient()
    {
        return array_keys($this->clients);
    }

    /**
     * Run a command against the Redis database.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function command($method, array $parameters = [])
    {
        return call_user_func_array([$this->clients['default'], $method], $parameters);
    }

    /**
     * Dynamically make a Redis command.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->command($method, $parameters);
    }

}
