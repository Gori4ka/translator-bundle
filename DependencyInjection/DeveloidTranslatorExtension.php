<?php

namespace Develoid\TranslatorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DeveloidTranslatorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('develoid_translator.default', $config['default']);

        if (isset($config['yandex'])) {
            $container->setParameter('develoid_translator.yandex_api_key', $config['yandex']['api_key']);
        }

        if (isset($config['google'])) {
            $container->setParameter('develoid_translator.google_api_key', $config['google']['api_key']);
        }

        if (isset($config['microsoft'])) {
            $container->setParameter('develoid_translator.microsoft_client_id', $config['microsoft']['client_id']);
            $container->setParameter('develoid_translator.microsoft_client_secret', $config['microsoft']['client_secret']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
