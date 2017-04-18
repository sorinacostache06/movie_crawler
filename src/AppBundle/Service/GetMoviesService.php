<?php
/**
 * Created by Sorina Costache.
 * User: sorina
 * Date: 18.04.2017*/

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DomCrawler\Crawler;
use Goutte\Client;
use AppBundle\Entity\Movie;

class GetMoviesService
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Foreach link find by crawler => getMovieFromLinks
     */
    public function insertMovies()
    {
        $repo = $this->em->getRepository('AppBundle:Link');
        $qb = $this->em->createQueryBuilder();
        $access_repo = $repo->selectAll($qb);
        $results = $access_repo->getQuery()->getResult();

        $this->getMovieFromLinks($results);
    }

    /**
     * Foreach link find by crawler checks if the link is associated to a movie
     * If it is a movie, save in database details such as link, title, year, rating, actors, directors and genre
     * @param $links
     */
    public function getMovieFromLinks($links)
    {
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
                            $crawler = $client->request('GET', $l);
                            $rate = $crawler->filter('div>div[class="left"]')->text();

                            if (strcmp($rate,"- -") == 0){
                                $movie->setRating(0);
                            } else {
                                $rates = explode('/',$rate);
                                $movie->setRating($rates[0]);
                            }

                            $rateImdb = ($crawler->filter('div[class="imdb-rating mt5 fsize11"]>a')->count())?
                                ($crawler->filter('div[class="imdb-rating mt5 fsize11"]>a')->text()):0;
                            $rateImdb = (float)str_replace("IMDB: ", '', $rateImdb);
                            $movie->setRatingImdb($rateImdb);

                            $titles_crowler = $crawler->filter('h1 > a');
                            foreach ($titles_crowler as $t) {
                                $title = $t->nodeValue;
                            }

                            $year = ($crawler->filter('a[class="link1"]')->count())
                                ?($crawler->filter('a[class="link1"]')->text()):' ';
                            $year = str_replace('(', '', $year);
                            $year = str_replace(')', '', $year);

                            $d = $crawler->filter('ul[class="list1"]')->children()->each(function (Crawler $node, $i) {
                                return $node->text();});

                            if (count($d) == 0) {
                                $actors = null;
                                $directors = null;
                            } elseif (count($d) == 1) {
                                $directorsWithoutSpaces = preg_replace('/\s+/', '', $d[0]);
                                if (strpos($directorsWithoutSpaces, "Regia") !== false) {
                                    $directorsWithoutRegie = str_replace("Regia", '', $directorsWithoutSpaces);
                                    $directors = explode(',',$directorsWithoutRegie);
                                } else {
                                    $directors = null;
                                }

                                $actorsWithoutSpaces = preg_replace('/\s+/', '', $d[0]);
                                if (strpos($actorsWithoutSpaces, "Cu") !== false) {
                                    $actorsWithoutCu = str_replace("Cu", '', $actorsWithoutSpaces);
                                    $actors = explode(',',$actorsWithoutCu);
                                } else {
                                    $actors = null;
                                }
                            } else {
                                $directorsWithoutSpaces = preg_replace('/\s+/', '', $d[0]);
                                if (strpos($directorsWithoutSpaces, "Regia") !== false) {
                                    $directorsWithoutRegie = str_replace("Regia", '', $directorsWithoutSpaces);
                                    $directors = explode(',',$directorsWithoutRegie);
                                } else {
                                    $directors = null;
                                }

                                $actorsWithoutSpaces = preg_replace('/\s+/', '', $d[1]);
                                if (strpos($actorsWithoutSpaces, "Cu") !== false) {
                                    $actorsWithoutCu = str_replace("Cu", '', $actorsWithoutSpaces);
                                    $actors = explode(',',$actorsWithoutCu);
                                } else {
                                    $actors = null;
                                }
                            }


                            $gen = ($crawler->filterXPath('//div[contains(@id, "movieGenreUserChoiceResults")]')->count())
                                ?($crawler->filterXPath('//div[contains(@id, "movieGenreUserChoiceResults")]')->text()):' ';

                            $genWithoutSpaces = preg_replace('/\s+/', ',', $gen);
                            $genres = explode(',',$genWithoutSpaces);
                            $genres = array_filter($genres);

                            $movie->setYear($year);
                            $movie->setTitle($title);
                            $movie->setLink($l);
                            $movie->setActors($actors);
                            $movie->setDirectors($directors);
                            $movie->setGenre($genres);
                            $this->em->persist($movie);
                            $this->em->flush();
                        }
                    }
                }
            }
        }
    }
}