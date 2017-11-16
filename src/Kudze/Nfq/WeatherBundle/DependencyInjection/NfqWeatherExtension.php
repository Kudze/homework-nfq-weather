<?php

namespace Kudze\Nfq\WeatherBundle\DependencyInjection;

use Kudze\Nfq\WeatherBundle\Core\Provider\CachedWeatherProvider;
use Kudze\Nfq\WeatherBundle\Core\Provider\DelegatingWeatherProvider;
use Kudze\Nfq\WeatherBundle\Core\Provider\IWeatherProvider;
use Kudze\Nfq\WeatherBundle\Core\Provider\OpenWeatherMapWeatherProvider;
use Kudze\Nfq\WeatherBundle\Core\Provider\YahooWeatherProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
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
        $loader = new Loader\XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $provider = $this->loadProvider($config['provider'], $config, $container);
        $container->set("nfq_weather.provider", $provider);
    }

    private function loadProvider(string $name, array $config, ContainerBuilder $container) : IWeatherProvider
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

                $provider = new YahooWeatherProvider();
                $container->set('nfq_weather.provider_yahoo', $provider);

                return $provider;

            case 'openweathermap':

                //Lets make sure that api_key is declared.
                if(!array_key_exists('api_key', $providerConfig))
                    throw new Exception('api_key is not configured under nfq_weather.providers.' . $name);

                $provider = new OpenWeatherMapWeatherProvider($providerConfig['api_key']);
                $container->set('nfq_weather.provider_openweathermap', $provider);

                return $provider;

            case 'delegating':

                if(!array_key_exists('providers', $providerConfig))
                    throw new Exception('providers is not configured under nfq_weather.providers.' . $name);

                if(empty($providerConfig['providers']))
                    throw new Exception('providers under nfq_weather.providers' . $name . ' is empty!');

                //Now we will interpret other providers
                $providers = array();
                foreach($providerConfig['providers'] as $val)
                    array_push($providers, $this->loadProvider($val, $config, $container));

                $provider = new DelegatingWeatherProvider($providers);
                $container->set('nfq_weather.provider_delegating', $provider);

                return $provider;

            case 'cached':

                if(!array_key_exists('provider', $providerConfig))
                    throw new Exception('provider is not configured under nfq_weather.providers.' . $name);

                if(!array_key_exists('ttl', $providerConfig))
                    throw new Exception('ttl is not configured under nfq_weather.providers.' . $name);

                $provider2 = $this->loadProvider($providerConfig['provider'], $config, $container);

                //Not really sure where to put this so for now this can stay here.
                $cache = new FilesystemAdapter();

                $provider = new CachedWeatherProvider($provider2, $cache);
                $container->set('nfq_weather.provider_cached', $provider);

                return $provider;

        }

        return null;

    }

}
