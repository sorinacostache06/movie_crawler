<?php
/**
 * Created by Sorina Costache.
 * User: sorina
 * Date: 18.04.2017*/

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Goutte\Client;
use AppBundle\Entity\Movie;

class HomeController extends Controller
{
    public function successAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $movie = new Movie();
        $repo = $em->getRepository('AppBundle:Movie');
        $movie = $repo->find(315);
        return $this->render(':Admin:success.html.twig',[]);
    }
}

