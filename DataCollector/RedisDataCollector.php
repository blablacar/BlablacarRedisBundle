<?php

namespace Blablacar\RedisBundle\DataCollector;

use Blablacar\RedisBundle\Redis\ClientLogger;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedisDataCollector extends DataCollector
{
    protected $clients = array();

    public function addClient($name, ClientLogger $client)
    {
        $this->clients[$name] = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        foreach ($this->clients as $name => $client) {
            foreach ($client->getCommands() as $command) {
                $this->data[] = array(
                    'command'    => $command['name'],
                    'arguments'  => implode(', ', $command['arguments']),
                    'duration'   => $command['duration'],
                    'connection' => $name
                );
            }

            $client->reset();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'redis';
    }

    public function getCommands()
    {
        return $this->data;
    }

    /**
     * getDuration
     *
     * @return int
     */
    public function getDuration()
    {
        $time = 0;
        foreach ($this->data as $data) {
            $time += $data['duration'];
        }

        return $time;
    }
}
