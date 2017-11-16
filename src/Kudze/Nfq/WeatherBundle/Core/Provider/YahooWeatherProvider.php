<?php
/**
 * Created by PhpStorm.
 * User: kkraujelis
 * Date: 10/12/17
 * Time: 6:33 PM
 */

namespace Kudze\Nfq\WeatherBundle\Core\Provider;


use Kudze\Nfq\WeatherBundle\Core\Location;
use Kudze\Nfq\WeatherBundle\Core\Util\CurlUtil;
use Kudze\Nfq\WeatherBundle\Core\Weather;

class YahooWeatherProvider implements IWeatherProvider
{

    const BASE_URL = 'http://query.yahooapis.com/v1/public/yql';

    /**
     * @param Location $location
     * @throws WeatherProviderException
     * @return Weather
     */
    public function fetch(Location $location): Weather
    {

        //Lets generate which URL to call.
        $url = self::generateRequestURL(self::generateRequestQuery($location));

        //Lets call URL and fetch results.
        $result = CurlUtil::callURL($url);

        //Lets parse the results
        $decodedResult = json_decode($result);

        //Some error handling
        if($decodedResult === null)
            throw new WeatherProviderException('Failed to connect to the YahooWeather servers!');

        if(count($decodedResult->{'query'}->{'results'}) === 0)
            throw new WeatherProviderException('Failed to fetch location by coordinates!');

        //Else we want to return weather
        $temp = $decodedResult->{'query'}->{'results'}->
            {'channel'}->{'item'}->{'condition'}->{'temp'};

        return new Weather($temp);
    }

    /**
     * @return string
     */
    private static function generateRequestURL(string $query, string $type = 'json') : string
    {
        return self::BASE_URL . '?q=' . urlencode($query) . '&format=' . $type;
    }


    /**
     * @return string
     */
    private static function generateRequestQuery(Location $location) : string
    {
        return
            'SELECT * FROM weather.forecast WHERE woeid IN 
            (SELECT woeid FROM geo.places WHERE text="(' . $location->getLatitude() . ',' . $location->getLongitude() . ')")
             AND u="c"';
    }

}

