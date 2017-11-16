<?php
/**
 * Created by PhpStorm.
 * User: kkraujelis
 * Date: 10/12/17
 * Time: 6:13 PM
 */

namespace Kudze\Nfq\WeatherBundle\Core;


class Weather
{

    private $temp;

    public function __construct(float $temp)
    {
        $this->temp = $temp;
    }

    public function getTemperature() : float { return $this->temp; }

}