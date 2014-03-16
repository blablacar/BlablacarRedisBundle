<?php

namespace Blablacar\RedisBundle\Tests\DependencyInjection;

use Blablacar\RedisBundle\Tests\TestCase;
use Blablacar\RedisBundle\DependencyInjection\BlablacarRedisExtension;

class BlablacarRedisExtensionTest extends TestCase
{
    public function test_it_is_initilizable()
    {
        $this->assertInstanceOf(
            'Blablacar\RedisBundle\DependencyInjection\BlablacarRedisExtension',
            new BlablacarRedisExtension()
        );
    }
}
