<?php

namespace Blablacar\RedisBundle\Tests\Redis;

use Blablacar\RedisBundle\Tests\TestCase;
use Blablacar\RedisBundle\Redis\ClientLogger;

class ClientLoggerTest extends TestCase
{
    public function test_it_is_initilizable()
    {
        $client = $this->prophet->prophesize('Blablacar\Redis\Client');

        $this->assertInstanceOf(
            'Blablacar\RedisBundle\Redis\ClientLogger',
            new ClientLogger($client->reveal())
        );
    }
}
