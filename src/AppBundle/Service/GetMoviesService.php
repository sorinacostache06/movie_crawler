<?php
/**
 * Created by Sorina Costache.
 * User: sorina
 * Date: 18.04.2017*/

namespace AppBundle\Service;

use AppBundle\Entity\Cinemagia;
use AppBundle\Entity\Rottentomatoes;
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
        $access_repo = $repo->selectLinks($qb);
        $results = $access_repo->getQuery()->getResult();

        $this->getMovieFromLinks($results);
        $this->matchMovies();

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
                $this->getMoviesFromCinemagia($l,$path_url);
            }
//            elseif (strcmp($host,"www.rottentomatoes.com") == 0){
//                $this->getMoviesFromTomatoes($l, $path_url);
//            }
        }

    }

    public function getMoviesFromCinemagia($l, $path_url)
    {
        $path = explode('/',$path_url);
        if (count($path) == 4 and strcmp($path[1],"filme") == 0 and strcmp($path[3],"") == 0) {
            $name = explode('-',$path[2]);
            if (is_numeric($name[count($name) -1]) and strcmp($name[0],"") != 0){
                $fragment = isset($url['fragment']) ? '#' . $url['fragment'] : '';
                if (strcmp($fragment,"") == 0) {
                    $repo = $this->em->getRepository('AppBundle:Cinemagia');
                    $qb = $this->em->createQueryBuilder();
                    $access_repo = $repo->distinctMovie($qb, $l);
                    $result = $access_repo->getQuery()->getResult();
                    if ($result == NULL) {
                        $movie = new Cinemagia();
                        $client = new Client();
                        $crawler = new Crawler();
                        $crawler = $client->request('GET', $l);
                        $rate = $crawler->filter('div>div[class="left"]')->text();

                        if (strcmp($rate, "- -") == 0) {
                            $movie->setRating(0);
                        } else {
                            $rates = explode('/', $rate);
                            $movie->setRating($rates[0]);
                        }

                        $rateImdb = ($crawler->filter('div[class="imdb-rating mt5 fsize11"]>a')->count()) ?
                            ($crawler->filter('div[class="imdb-rating mt5 fsize11"]>a')->text()) : 0;
                        $rateImdb = (float)str_replace("IMDB: ", '', $rateImdb);
                        $movie->setRatingImdb($rateImdb);

                        $titles_crowler = $crawler->filter('h1 > a');
                        foreach ($titles_crowler as $t) {
                            $title = $t->nodeValue;
                        }

                        $year = ($crawler->filter('a[class="link1"]')->count())
                            ? ($crawler->filter('a[class="link1"]')->text()) : ' ';
                        $year = str_replace('(', '', $year);
                        $year = str_replace(')', '', $year);

                        $d = $crawler->filter('ul[class="list1"]')->children()->each(function (Crawler $node, $i) {
                            return $node->text();
                        });


                        if (count($d) == 0) {
                            $actors = null;
                            $directors = null;
                        } elseif (count($d) == 1) {
                            $directorsWithoutSpaces = preg_replace('/\s+/', '', $d[0]);
                            if (strpos($directorsWithoutSpaces, "Regia") !== false) {
                                $directorsWithoutRegie = str_replace("Regia", '', $directorsWithoutSpaces);
                                $directorsWithoutSpace = explode(',', $directorsWithoutRegie);
                                $directors = [];
                                $i = 0;
                                foreach ($directorsWithoutSpace as $director) {
                                    $e = preg_split('/(?=[A-Z])/', $director);
                                    $directors[$i++] = implode(" ", $e);
                                }
                            } else {
                                $directors = null;
                            }

                            $actorsWithoutSpaces = preg_replace('/\s+/', '', $d[0]);
                            if (strpos($actorsWithoutSpaces, "Cu") !== false) {
                                $actorsWithoutCu = str_replace("Cu", '', $actorsWithoutSpaces);
                                $actorsWithoutSpace = explode(',', $actorsWithoutCu);
                                $actors = [];
                                $i = 0;
                                foreach ($actorsWithoutSpace as $actor) {
                                    $e = preg_split('/(?=[A-Z])/', $actor);
                                    $actors[$i++] = implode(" ", $e);
                                }

                            } else {
                                $actors = null;
                            }
                        } else {
                            $directorsWithoutSpaces = preg_replace('/\s+/', '', $d[0]);
                            if (strpos($directorsWithoutSpaces, "Regia") !== false) {
                                $directorsWithoutRegie = str_replace("Regia", '', $directorsWithoutSpaces);
                                $directorsWithoutSpace = explode(',', $directorsWithoutRegie);
                                $directors = [];
                                $i = 0;
                                foreach ($directorsWithoutSpace as $director) {
                                    $e = preg_split('/(?=[A-Z])/', $director);
                                    $directors[$i++] = implode(" ", $e);
                                }
                            } else {
                                $directors = null;
                            }

                            $actorsWithoutSpaces = preg_replace('/\s+/', '', $d[1]);
                            if (strpos($actorsWithoutSpaces, "Cu") !== false) {
                                $actorsWithoutCu = str_replace("Cu", '', $actorsWithoutSpaces);
                                $actorsWithoutSpace = explode(',', $actorsWithoutCu);
                                $actors = [];
                                $i = 0;
                                foreach ($actorsWithoutSpace as $actor) {
                                    $e = preg_split('/(?=[A-Z])/', $actor);
                                    $actors[$i++] = implode(" ", $e);
                                }
                            } else {
                                $actors = null;
                            }
                        }

                        $gen = ($crawler->filterXPath('//div[contains(@id, "movieGenreUserChoiceResults")]')->count())
                            ? ($crawler->filterXPath('//div[contains(@id, "movieGenreUserChoiceResults")]')->text()) : null;

                        $genWithoutSpaces = preg_replace('/\s+/', ',', $gen);
                        $genres = explode(',', $genWithoutSpaces);
                        $genres = array_filter($genres);

                        $image = ($crawler->filter('a>img[class="img2 mb5"]')->count()) ?
                            ($image = $crawler->filter('a>img[class="img2 mb5"]')->image()->getUri()) : null;

                        $movie->setImage($image);
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

    public function getMoviesFromTomatoes($l, $path_url)
    {
        $path = explode('/',$path_url);

        if (count($path) == 3 and strcmp($path[1],"m") == 0) {
            $repo = $this->em->getRepository('AppBundle:Rottentomatoes');
            $qb = $this->em->createQueryBuilder();
            $access_repo = $repo->distinctMovie($qb,$l);
            $result = $access_repo->getQuery()->getResult();
            if ($result == NULL) {
                $movie = new Rottentomatoes();
                $client = new Client();
                $crawler = new Crawler();
                $crawler = $client->request('GET', $l);
                $title = ($crawler->filter('h1[class="title hidden-xs"]')->count())
                    ?($crawler->filter('h1[class="title hidden-xs"]')->text()) : null;
                if ($title != null) {
                    $title = substr_replace($title,"",-7);
                    $title = ltrim($title);
                }

                $rating = ($crawler->filter('div[class="superPageFontColor"]')->count())
                    ?($crawler->filter('div[class="superPageFontColor"]')->text()) : null;
                if ($rating != null){
                    $rating = preg_replace('/\s+/', '', $rating);
                    $rating = preg_replace('/[^0-9]/', '', $rating);
                    $rating = substr_replace($rating,"",-2);
                }

                $qb1 = $this->em->createQueryBuilder();
                $access_repo = $repo->distinctTitle($qb1,$title);
                $result1 = $access_repo->getQuery()->getResult();
                if ($title != null && $result1 == null) {
                    $movie->setTitle($title);
                    $movie->setRating($rating);
                    $movie->setLink($l);
                    $this->em->persist($movie);
                    $this->em->flush();
                }
            }
        }
    }

    public function matchMovies()
    {

        $repo = $this->em->getRepository('AppBundle:Cinemagia');
        $qb = $this->em->createQueryBuilder();
        $access_repo = $repo->selectAllMovies($qb);
        $cineMovies = $access_repo->getQuery()->getResult();
        foreach ($cineMovies as $cineMovie)
        {
            $repoRotten = $this->em->getRepository('AppBundle:Rottentomatoes');
            $qb2 = $this->em->createQueryBuilder();
            $rottenMovie = $repoRotten->findOneByTitle($cineMovie->getTitle());
            if ($rottenMovie != null) {
                if ($rottenMovie->getRating() > 10) {
                    $rottenRating = ((float)$rottenMovie->getRating()/10);
                } else $rottenRating = $rottenMovie->getRating();

                if ($rottenMovie->getRating() == null) {
                    $rottenRating = 0;
                }

                $rating = (float)($cineMovie->getRating() + $cineMovie->getRatingImdb() + $rottenRating)/3;
                $rating = number_format($rating,2);

                $repoRotten = $this->em->getRepository('AppBundle:Movie');
                $qb3 = $this->em->createQueryBuilder();
                $movieDist = $repoRotten->distinctMovie($qb3,$cineMovie->getTitle());
                $result = $movieDist->getQuery()->getResult();

                if ($result == null) {
                    $movie = new Movie();
                    $movie->setLink($cineMovie->getLink());
                    $movie->setTitle($cineMovie->getTitle());
                    $movie->setYear($cineMovie->getYear());
                    $movie->setActors($cineMovie->getActors());
                    $movie->setDirectors($cineMovie->getDirectors());
                    $movie->setGenre($cineMovie->getGenre());
                    $movie->setImage($cineMovie->getImage());
                    $movie->setRatingCinemagia($cineMovie->getRating());
                    $movie->setRatingImdb($cineMovie->getRatingImdb());
                    $movie->setRatingRotten($rottenRating);
                    $movie->setRating($rating);
                    $cineMovie->setWasMatched(true);
                    $rottenMovie->setWasMatched(true);
                    $this->em->persist($movie);
                    $this->em->persist($cineMovie);
                    $this->em->persist($rottenMovie);
                    $this->em->flush();
                }
            }
            else {
                $repoRotten = $this->em->getRepository('AppBundle:Movie');
                $qb3 = $this->em->createQueryBuilder();
                $movieDist = $repoRotten->distinctMovie($qb3,$cineMovie->getTitle());
                $result = $movieDist->getQuery()->getResult();
                if ($result == NULL) {
                    $movie = new Movie();
                    $movie->setLink($cineMovie->getLink());
                    $movie->setTitle($cineMovie->getTitle());
                    $movie->setYear($cineMovie->getYear());
                    $movie->setActors($cineMovie->getActors());
                    $movie->setDirectors($cineMovie->getDirectors());
                    $movie->setGenre($cineMovie->getGenre());
                    $movie->setImage($cineMovie->getImage());
                    $movie->setRatingCinemagia($cineMovie->getRating());
                    $movie->setRatingImdb($cineMovie->getRatingImdb());
                    $movie->setRatingRotten(0);
                    $rating = ($cineMovie->getRatingImdb() + $cineMovie->getRating())/2;
                    $rating = number_format($rating,2);
                    $movie->setRating($rating);
                    $cineMovie->setWasMatched(false);
//                    $rottenMovie->setWasMatched(false);
                    $this->em->persist($movie);
                    $this->em->persist($cineMovie);
//                    $this->em->persist($rottenMovie);
                    $this->em->flush();
                }

            }
        }
    }
}
