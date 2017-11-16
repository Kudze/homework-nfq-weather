<?php
/**
 * Created by PhpStorm.
 * User: kkraujelis
 * Date: 10/12/17
 * Time: 6:41 PM
 */

namespace Kudze\Nfq\WeatherBundle\Core\Provider;


use Kudze\Nfq\WeatherBundle\Core\Location;
use Kudze\Nfq\WeatherBundle\Core\Weather;
use Psr\Log\LoggerInterface;

class DelegatingWeatherProvider implements IWeatherProvider
{

    private $weatherProviders;
    private $logger;

    public function __construct(array $weatherProvider, LoggerInterface $logger) {

        $this->weatherProviders = $weatherProvider;
        $this->logger = $logger;

    }


    /**
     * @param Location $location
     * @throws WeatherProviderException
     * @return Weather
     */
    public function fetch(Location $location): Weather
    {
        foreach($this->weatherProviders as $weatherProvider) {

            try {
                return $weatherProvider->fetch($location);
            }

            catch(WeatherProviderException $e) {

                $this->logger->notice('Weather Provider failed to fetch result, reason:' . $e->getMessage());

                continue;

            }

        }

        throw new WeatherProviderException("All provided weather providers failed to fetch results.");
    }

    public function getProviders() : array {
        return $this->weatherProviders;
    }

    public function getLogger() : LoggerInterface {
        return $this->logger;
    }
}