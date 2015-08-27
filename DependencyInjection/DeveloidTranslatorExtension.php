<?php

namespace Develoid\TranslatorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DeveloidTranslatorExtension extends Extension
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('develoid_translator.default', $config['default']);

        if (isset($config['yandex'])) {
            $this->createDefinition(
                $container->getParameter('develoid_translator_yandex_translator_class'),
                'yandex',
                [
                    $config['yandex']['api_key']
                ])
            ;
        }

        if (isset($config['google'])) {
            $this->createDefinition(
                $container->getParameter('develoid_translator_google_translator_class'),
                'google',
                [
                    $config['google']['api_key']
                ])
            ;
        }

        if (isset($config['microsoft'])) {
            $this->createDefinition(
                $container->getParameter('develoid_translator_microsoft_translator_class'),
                'microsoft',
                [
                    $config['microsoft']['client_id'],
                    $config['microsoft']['client_secret']
                ])
            ;
        }
    }

    /**
     * @param $class
     * @param $type
     * @param array $arguments
     */
    private function createDefinition($class, $type, array $arguments)
    {
        $definition = new Definition($class, $arguments);
        $this->container->setDefinition(sprintf('develoid_translator.%s_translator', $type), $definition);
    }
}
