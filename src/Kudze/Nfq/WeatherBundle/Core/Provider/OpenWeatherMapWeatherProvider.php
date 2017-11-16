<?php
/**
 * Created by PhpStorm.
 * User: kkraujelis
 * Date: 10/12/17
 * Time: 6:49 PM
 */

namespace Kudze\Nfq\WeatherBundle\Core\Provider;


use Kudze\Nfq\WeatherBundle\Core\Location;
use Kudze\Nfq\WeatherBundle\Core\Util\CurlUtil;
use Kudze\Nfq\WeatherBundle\Core\Weather;

class OpenWeatherMapWeatherProvider implements IWeatherProvider
{

    const BASE_URL = 'http://api.openweathermap.org/data/2.5/';
    private $APIKey;

    public function __construct(string $APIKey)
    {
        $this->APIKey = $APIKey;
    }

    /**
     * @param Location $location
     * @throws WeatherProviderException
     * @return Weather
     */
    public function fetch(Location $location): Weather
    {

        //Lets generate request URL
        $url = $this->generateRequestURL($location);

        //Lets call the URL
        $result = CurlUtil::callURL($url);

        //Lets parse the results
        $decodedResult = json_decode($result);

        //Some error handling
        if($decodedResult === null)
            throw new WeatherProviderException('Failed to connect to the OpenWeatherMap servers!');

        if($decodedResult->{'cod'} === 401)
            throw new WeatherProviderException('Provided API key is invalid');

        $temp = $decodedResult->{'main'}->{'temp'};

        return new Weather($temp);

    }

    public function getAPIKey() : string
    {
        return $this->APIKey;
    }

    private function generateRequestURL(Location $location)
    {
        return self::BASE_URL .
            'weather?lat=' . $location->getLatitude() . '&lon=' . $location->getLongitude() .
            '&units=metric&APPID=' . $this->APIKey;
    }

}