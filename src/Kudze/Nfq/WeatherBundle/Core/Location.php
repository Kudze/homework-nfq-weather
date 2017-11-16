<?php
/**
 * Created by PhpStorm.
 * User: kkraujelis
 * Date: 10/12/17
 * Time: 6:09 PM
 */

namespace Kudze\Nfq\WeatherBundle\Core;

class Location {

    private $lon, $lat;

    public function __construct(float $lat, float $lon)
    {
        $this->lon = $lon;
        $this->lat = $lat;
    }

    public function getLongitude() : float { return $this->lon; }
    public function getLatitude() : float { return $this->lat; }

}