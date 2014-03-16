<?php

namespace Blablacar\RedisBundle\Tests\DependencyInjection;

use Blablacar\RedisBundle\Tests\TestCase;
use Blablacar\RedisBundle\DependencyInjection\Configuration;

class ConfigurationTest extends TestCase
{
    public function test_it_is_initilizable()
    {
        $this->assertInstanceOf(
            'Blablacar\RedisBundle\DependencyInjection\Configuration',
            new Configuration()
        );
    }
}
