<?php

namespace Kudze\Nfq\WeatherBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class NfqWeatherExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        //Services
        $loader = new Loader\XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');

        //Config
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        //Config intervention with services.
        $name = $this->loadProvider($config['provider'], $config, $container);
        $container->setDefinition('nfq_weather.provider', $container->getDefinition($name));
    }

    private function loadProvider(string $name, array $config, ContainerBuilder $container) : string
    {

        //Before we do anything lets make sure that its even configured under providers.
        if(!array_key_exists($name, $config['providers']))
            throw new Exception($name . ' is not configured under nfq_weather.providers');
        $providerConfig = $config['providers'][$name];

        switch($name) {

            case 'yahoo':

                //Lets make sure that api_key is declared.
                if(!array_key_exists('api_key', $providerConfig))
                    throw new Exception('api_key is not configured under nfq_weather.providers.' . $name);

                $def = $container->getDefinition('nfq_weather.yahoo_provider');

                return 'nfq_weather.yahoo_provider';

            case 'openweathermap':

                //Lets make sure that api_key is declared.
                if(!array_key_exists('api_key', $providerConfig))
                    throw new Exception('api_key is not configured under nfq_weather.providers.' . $name);

                $def = $container->getDefinition('nfq_weather.openweathermap_provider');
                $def->replaceArgument(0, $providerConfig['api_key']);

                return 'nfq_weather.openweathermap_provider';

            case 'delegating':

                if(!array_key_exists('providers', $providerConfig))
                    throw new Exception('providers is not configured under nfq_weather.providers.' . $name);

                if(empty($providerConfig['providers']))
                    throw new Exception('providers under nfq_weather.providers' . $name . ' is empty!');

                //Now we will interpret other providers
                $references = array();
                foreach($providerConfig['providers'] as $val) {
                    $name = $this->loadProvider($val, $config, $container);

                    array_push($references, new Reference($name));
                }

                $def = $container->getDefinition('nfq_weather.delegating_provider');
                $def->setArgument(0, $references);

                return 'nfq_weather.delegating_provider';

            case 'cached':

                if(!array_key_exists('provider', $providerConfig))
                    throw new Exception('provider is not configured under nfq_weather.providers.' . $name);

                if(!array_key_exists('ttl', $providerConfig))
                    throw new Exception('ttl is not configured under nfq_weather.providers.' . $name);

                $name = $this->loadProvider($providerConfig['provider'], $config, $container);
                $def = $container->getDefinition('nfq_weather.cached_provider');
                $def->setArgument(0, new Reference($name));
                $def->setArgument(1, $providerConfig['ttl']);
                $def->setArgument(2, new Reference('nfq_weather.cache'));

                return 'nfq_weather.cached_provider';

        }

        return null;

    }

}
