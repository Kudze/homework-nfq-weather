<?php

namespace Kudze\Nfq\WeatherBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nfq_weather')
            ->children()

                ->enumNode('provider')
                    ->values(array('yahoo', 'openweathermap', 'delegating', 'cached'))
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()

                ->arrayNode('providers')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()

                        ->arrayNode('yahoo')
                            ->children()
                                ->scalarNode('api_key')
                                    ->defaultValue('')
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('openweathermap')
                            ->children()
                                ->scalarNode('api_key')
                                    ->defaultValue('')
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('delegating')
                            ->children()
                                ->arrayNode('providers')
                                    ->enumPrototype()
                                        ->values(array('yahoo', 'openweathermap')) //To put cached here doesnt make any sense
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('cached')
                            ->children()
                                ->enumNode('provider')
                                    ->values(array('yahoo', 'openweathermap', 'delegating'))
                                ->end()

                                ->integerNode('ttl')
                                    ->min(1)
                                ->end()
                            ->end()
                        ->end()

                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
