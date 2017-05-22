<?php

namespace Develoid\TranslatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('develoid_translator');
        $rootNode->children()
            ->scalarNode('default')
                ->defaultValue('google')
                ->isRequired()
                ->validate()
                    ->ifNotInArray(['google', 'microsoft', 'yandex'])
                    ->thenInvalid('Invalid translator type %s')
                ->end()
            ->end()
            ->arrayNode('google')
                ->children()
                    ->booleanNode('enabled')
                        ->defaultFalse()
                    ->end()
                    ->scalarNode('api_key')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('yandex')
                ->children()
                    ->booleanNode('enabled')
                        ->defaultFalse()
                    ->end()
                    ->scalarNode('api_key')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('microsoft')
                ->children()
                    ->booleanNode('enabled')
                        ->defaultFalse()
                    ->end()
                    ->scalarNode('api_key')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
