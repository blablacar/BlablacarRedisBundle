<?php

namespace Blablacar\RedisBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * BlablacarRedisExtension
 */
class BlablacarRedisExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('redis.xml');

        $enableLogger = $config['enable_logger'];
        if ($enableLogger) {
            $loader->load('collector.xml');
        }

        foreach ($config['clients'] as $name => $config) {
            $id = sprintf('blablacar_redis.client.%s', $name);

            $baseClientDefinition = new DefinitionDecorator('blablacar_redis.client.base');
            $baseClientDefinition
                ->replaceArgument(0, $config['host'])
                ->replaceArgument(1, $config['port'])
                ->replaceArgument(2, $config['base'])
            ;

            if (!$enableLogger) {
                $container->setDefinition($id, $baseClientDefinition);
            } else {
                $container->setDefinition($id.'.base', $baseClientDefinition)->setPublic(false);

                $container
                    ->setDefinition($id, new DefinitionDecorator('blablacar_redis.client.logger'))
                    ->replaceArgument(0, new Reference($id.'.base'))
                ;
                $container
                    ->getDefinition('blablacar_redis.data_collector')
                    ->addMethodCall('addClient', array($name, new Reference($id)))
                ;
            }
        }

        if (isset($config['session'])) {
            $loader->load('session.xml');

            $container->setParameter('blablacar_redis.session.prefix', $config['session']['prefix']);
            $container->setParameter('blablacar_redis.session.ttl', $config['session']['ttl']);
            $container->setParameter('blablacar_redis.session.spin_lock_wait', $config['session']['spin_lock_wait']);
            $container->setParameter('blablacar_redis.session.lock_max_wait', $config['session']['lock_max_wait']);

            $client = sprintf('blablacar_redis.client_%s', $config['session']['client']);
            $container->setAlias('blablacar_redis.session.client', $client);
        }
    }
}
