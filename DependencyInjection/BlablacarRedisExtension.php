<?php

namespace Blablacar\RedisBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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

        foreach ($config['clients'] as $name => $clientConfig) {
            $id = sprintf('blablacar_redis.client.%s', $name);

            $baseClientDefinition = new ChildDefinition('blablacar_redis.client.base');
            $baseClientDefinition
                ->replaceArgument(0, $clientConfig['host'])
                ->replaceArgument(1, $clientConfig['port'])
            ;

            if (isset($clientConfig['timeout'])) {
                $baseClientDefinition->replaceArgument(2, $clientConfig['timeout']);
            }

            if (isset($clientConfig['base'])) {
                $baseClientDefinition->replaceArgument(3, $clientConfig['base']);
            }

            if (isset($clientConfig['password'])) {
                $baseClientDefinition->replaceArgument(4, $clientConfig['password']);
            }

            $baseClientDefinition->addTag('redis.client', array('client_name' => $name));

            if (!$enableLogger) {
                $container->setDefinition($id, $baseClientDefinition)->setPublic($config['public']);
            } else {
                $container->setDefinition($id.'.base', $baseClientDefinition)->setPublic(false);

                $container
                    ->setDefinition($id, new ChildDefinition('blablacar_redis.client.logger'))
                    ->replaceArgument(0, new Reference($id.'.base'))
                    ->setPublic($config['public'])
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

            $client = sprintf('blablacar_redis.client.%s', $config['session']['client']);
            $container->setAlias('blablacar_redis.session.client', $client);
            $container->setAlias('session.handler', 'blablacar_redis.session_handler');
        }
    }
}
