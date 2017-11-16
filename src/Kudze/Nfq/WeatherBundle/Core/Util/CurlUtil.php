<?php
/**
 * Created by PhpStorm.
 * User: kkraujelis
 * Date: 10/17/17
 * Time: 3:08 PM
 */

namespace Kudze\Nfq\WeatherBundle\Core\Util;


class CurlUtil
{

    /**
     * @param string $url
     * @return string
     */
    public static function callURL(string $url) : string {

        //Lets configure call.
        $session = curl_init($url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER,true);

        //Lets execute call.
        return curl_exec($session);

    }


}