<?php
/**
 * Created by PhpStorm.
 * User: kkraujelis
 * Date: 10/17/17
 * Time: 3:01 PM
 */

namespace Kudze\Nfq\WeatherBundle\Core\Provider;

use Kudze\Nfq\WeatherBundle\Core\Location;
use Kudze\Nfq\WeatherBundle\Core\Weather;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachedWeatherProvider implements IWeatherProvider
{

    private $provider;
    private $ttl;
    private $cacheItemPool;
    private $cachePrefix;

    public function __construct(IWeatherProvider $provider, int $ttl, CacheItemPoolInterface $cacheItemPool, string $cachePrefix = 'nfq.weather')
    {
        $this->provider = $provider;
        $this->ttl = $ttl;
        $this->cacheItemPool = $cacheItemPool;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * @param Location $location
     * @throws WeatherProviderException
     * @return Weather
     */
    public function fetch(Location $location): Weather
    {

        $currCacheItem = $this->getCacheItemByLocation($location);

        //If we didn't hit cache then we want to renew it.
        if(!$currCacheItem->isHit()) {

            $weather = $this->provider->fetch($location);

            //OpenWeatherMap suggests 10 minute delay. So lets use it.
            $currCacheItem->set($weather);
            $currCacheItem->expiresAfter($this->ttl);
            $this->cacheItemPool->save($currCacheItem);

        }

        return $currCacheItem->get();

    }

    private function getCacheItemByLocation(Location $location) : CacheItemInterface
    {
        return
            $this->cacheItemPool->getItem(
                $this->cachePrefix .
                '.' . $location->getLatitude() .
                '.' . $location->getLongitude()
            );
    }

    public function getWeatherProvider() : IWeatherProvider
    {
        return $this->provider;
    }

    public function getTimeToLive() : int
    {
        return $this->ttl;
    }

    public function getCacheItemPool() : CacheItemPoolInterface
    {
        return $this->cacheItemPool;
    }

    public function getCachePrefix() : string
    {
        return $this->cachePrefix;
    }

}