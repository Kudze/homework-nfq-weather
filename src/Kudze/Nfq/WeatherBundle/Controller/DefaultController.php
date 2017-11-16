<?php

namespace Kudze\Nfq\WeatherBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('NfqWeatherBundle:Default:index.html.twig');
    }
}
