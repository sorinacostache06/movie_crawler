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
    public function listMoviesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Movie');
        $qb = $em->createQueryBuilder();
        $movies = $repo->selectAll($qb);
        $movieManageList = $movies->getQuery()->getResult();

        if (empty($movieManageList)) {
            $this->addFlash('notice', $this->get('translator')->trans('movie_list_empty'));
        }

        return $this->render(
            '::movie_list.html.twig',
            [
                'movieManageList' => $movieManageList,
            ]
        );
    }
}

