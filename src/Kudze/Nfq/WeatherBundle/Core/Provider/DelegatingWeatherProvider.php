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

class DelegatingWeatherProvider implements IWeatherProvider
{

    private $weatherProviders;

    public function __construct(array $weatherProvider) {

        $this->weatherProviders = $weatherProvider;

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

                printf('Weather Provider failed to fetch result, reason: %s' . "\n", $e->getMessage());

                continue;

            }

        }

        throw new WeatherProviderException("All provided weather providers failed to fetch results.");
    }
}