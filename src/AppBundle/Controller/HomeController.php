<?php
/**
 * Created by Sorina Costache.
 * User: sorina
 * Date: 18.04.2017*/

namespace AppBundle\Controller;

use AppBundle\Form\AddToWatchType;
use AppBundle\Form\MovieFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Goutte\Client;
use AppBundle\Entity\Movie;

class HomeController extends Controller
{
    public function listMoviesAction(Request $request)
    {
        $filterForm = $this->createForm(MovieFilterType::class);
        $manageForm = $this->createForm(AddToWatchType::class);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Movie');

        $filterForm->handleRequest($request);
        $qb = $em->createQueryBuilder();

        if ($filterForm->isSubmitted()) {
            if ($filterForm->isValid()) {
                $movieFilters = $request->query->get('movie_filter');
                $mov = $repo->getMoviesByFilters($movieFilters);
                $pages = $movieFilters['results'];
            }else {
                $this->addFlash('error', 'Form-ul este invalid');
                $pages = 10;
                $mov = $repo->selectAll($qb);
            }
        } else {
            $pages = 10;
            $mov = $repo->selectAll($qb);
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $mov,
            $request->query->getInt('page',1),
            $pages
        );


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $mov,
            $request->query->getInt('page',1),
            $pages
        );

        $qb = $em->createQueryBuilder();
        $movies = $repo->selectAll($qb);
        $movieManageList = $movies->getQuery()->getResult();
        if (empty($movieManageList)) {
            $this->addFlash('notice', $this->get('translator')->trans('movie_list_empty'));
        }

        return $this->render(
            '::movie_list.html.twig',
            [
                'manageForm' =>$manageForm->createView(),
                'filterForm' => $filterForm->createView(),
                'movieManageList' => $movieManageList,
                'pagination' =>$pagination
            ]
        );
    }

    /**
     * Foreach link find by crawler => getMovieFromLinks
     */
    public function insertMovies1Action()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Link');
        $qb = $em->createQueryBuilder();
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
                            $em = $this->getDoctrine()->getManager();
                            $repo = $em->getRepository('AppBundle:Movie');
                            $qb = $em->createQueryBuilder();
                            $access_repo = $repo->distinctLink($qb, $l);
                            $result = $access_repo->getQuery()->getResult();
                            if ($result == NULL) {
                                $movie = new Movie();
                                $client = new Client();
                                $crawler = new Crawler();
                                $crawler = $client->request('GET', 'http://www.cinemagia.ro/filme/die-haschenschule-jagd-nach-dem-goldenen-ei-1800305/');
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
                                    ? ($crawler->filterXPath('//div[contains(@id, "movieGenreUserChoiceResults")]')->text()) : ' ';

                                $genWithoutSpaces = preg_replace('/\s+/', ',', $gen);
                                $genres = explode(',', $genWithoutSpaces);
                                $genres = array_filter($genres);

                                $image = $crawler->filter('a>img[class="img2 mb5"]')->image()->getUri();
                                var_dump($image);
                                die();

                                $movie->setYear($year);
                                $movie->setTitle($title);
                                $movie->setLink($l);
                                $movie->setActors($actors);
                                $movie->setDirectors($directors);
                                $movie->setGenre($genres);
                                $em = $this->getDoctrine()->getManager();
                                $em->persist($movie);
                                $em->flush();
                            }
                        }
                    }
                }
            }
        }
    }
}

