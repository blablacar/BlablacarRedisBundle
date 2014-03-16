<?php

namespace Blablacar\RedisBundle\Tests\DataCollector;

use Blablacar\RedisBundle\Tests\TestCase;
use Blablacar\RedisBundle\DataCollector\RedisDataCollector;

class RedisDataCollectorTest extends TestCase
{
    public function test_it_is_initilizable()
    {
        $this->assertInstanceOf(
            'Blablacar\RedisBundle\DataCollector\RedisDataCollector',
            new RedisDataCollector($client->reveal())
        );
    }
}
