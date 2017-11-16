<?php

namespace Kudze\Nfq\WeatherBundle\Controller;

use Kudze\Nfq\WeatherBundle\Core\Location;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $location = new Location(54.68, 25.27);
        $provider = $this->container->get('nfq_weather.provider');
        $weather = $provider->fetch($location);

        return $this->render('NfqWeatherBundle:Default:index.html.twig', [
            'location' => $location,
            'weather' => $weather
        ]);
    }
}
