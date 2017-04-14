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
use AppBundle\Entity\Movie;

class HomeController extends Controller
{
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
                    if (is_numeric($name[count($name) -1]) and strcmp($name[0],"") != 0){
                        $fragment = isset($url['fragment']) ? '#' . $url['fragment'] : '';
                        if (strcmp($fragment,"") == 0) {
                            $movie = new Movie();
                            $client = new Client();
                            $crawler = new Crawler();
                            $crawler = $client->request('GET', 'http://www.cinemagia.ro/filme/la-fille-inconnue-622701/');
                            $rate = $crawler->filter('div>div[class="left"]')->text();
                            if (strcmp($rate,"- -") == 0){
                                  $rate = 0;
//                                $movie->setRating(0);
                            } else {
                                   $rates = explode('/',$rate);
                                // $movie->setRating($rates[0]);
                                    $rate = $rates[0];
                            }
                            $titles_crowler = $crawler->filter('h1 > a');
                            $year = $crawler->filter('a[class="link1"]')->text();
                            foreach ($titles_crowler as $t) {
                                $title = $t->nodeValue;
                        }
//                            $movie->setYear($year);
//                            $movie->setTitle($title);
//                            $movie->setLink($l);
                            $d = $crawler->filter('ul[class="list1"]')->children()->each(function (Crawler $node, $i) {
                                return $node;});
//                            $movie->setDirectors($d[0]);
//                            $movie->setActors($d[1]);
                        }
                        }
                    }
                }
            }
        }
    }
