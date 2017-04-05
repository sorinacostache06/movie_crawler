<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Goutte\Client;
use AppBundle\Entity\Test;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;

class HomeController extends Controller
{
    public $arrayLinks = [];

    public $countLinks = 0;

    public function successAction(Request $request)
    {
        return $this->render(':Admin:success.html.twig',[]);
    }

    public function crowAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Test');
        $qb = $em->createQueryBuilder();
        $access_repo = $repo->selectAll($qb);
        $results = $access_repo->getQuery()->getResult();

        $this->getMovieFromLinks($results);

        return new Response('Welcome!');
    }

    public function startCrawlerAction()
    {
        $this->getAllDistinctLinks('http://www.cinemagia.ro/');
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Test');
        $qb = $em->createQueryBuilder();
        $access_repo = $repo->selectAll($qb);
        $results = $access_repo->getQuery()->getResult();

        set_time_limit (300);
        foreach ($results as $result) {
            $url = $result->getLink();
            $this->getAllDistinctLinks($url);
        }
    }

    public function getAllDistinctLinks($siteUrl)
    {

        $client = new Client();
        $crawler = new Crawler();
        $crawler = $client->request('GET', $siteUrl);

        $links = $crawler->filter('a')->links();
        foreach ($links as $link) {
            $l = $link->getUri();
            $url = parse_url($l);
            $host = (isset($url['host'])) ? $url['host'] : '';
            if (strcmp($host,"www.cinemagia.ro") == 0) {
                $em = $this->getDoctrine()->getManager();
                $repo = $em->getRepository('AppBundle:Test');
                $qb = $em->createQueryBuilder();
                $access_repo = $repo->distinctLink($qb, $l);
                $result = $access_repo->getQuery()->getResult();
                if ($result == NULL) {
                    $this->arrayLinks[$this->countLinks++] = $l;
                    $test = new Test();
                    $test->setLink($l);
                    $em->persist($test);
                    $em->flush();
                }
            }
        }
    }


    public function getMovieFromLinks($links)
    {
        //get year and get name
//        $titles_crowler = $crawler->filter('h1 > a');
//                        $year = $crawler->filter('a[class="link1"]')->text();
//                        foreach ($titles_crowler as $t) {
//                            $title = $t->nodeValue;
//                        }
//
//                        echo $l . " " . $year . " " . $title . "<br/>";

        foreach ($links as $link) {
            $l = $link->getLink();
            $url = parse_url($l);
            $host = (isset($url['host'])) ? $url['host'] : '';
            $path_url = (isset($url['path'])) ? $url['path'] : '';
            if (strcmp($host,"www.cinemagia.ro") == 0) {
                $path = explode('/',$path_url);
                if (count($path) == 4 and strcmp($path[1],"filme") == 0 and strcmp($path[3],"") == 0) {
                    $name = explode('-',$path[2]);
                    if (is_numeric($name[count($name) -1])){
                        echo $l . '    ' . count($path) . "<br/>";
//                        $em = $this->getDoctrine()->getManager();
//                        $repo = $em->getRepository('AppBundle:Test');
//                        $qb = $em->createQueryBuilder();
//                        $access_repo = $repo->distinctLink($qb, $l);
//                        $result = $access_repo->getQuery()->getResult();
//                        if ($result == NULL) {
//                            $test = new Test();
//                            $test->setLink($l);
//                            $em->persist($test);
//                            $em->flush();
//                        }
                    }
                }
            }
        }
    }
}