<?php

namespace Blablacar\RedisBundle\Redis;

use Blablacar\Redis\Client;

class ClientLogger extends Client
{
    protected $client;

    protected $commands = array();

    /**
     * __construct
     *
     * @param Client $client
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * __call
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        $start = microtime(true);
        $return = $this->client->__call($name, $arguments);
        $duration = microtime(true) - $start;

        $this->commands[] = array(
            'name'      => $name,
            'arguments' => implode(', ', $arguments),
            'duration'  => $duration
        );

        return $return;
    }

    /**
     * getCommands
     *
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }
}
