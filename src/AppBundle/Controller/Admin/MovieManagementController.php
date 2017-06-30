<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Cinemagia;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Rottentomatoes;
use AppBundle\Form\MovieEditType;
use AppBundle\Form\MovieManagementFilterType;
use AppBundle\Form\MovieManagementType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MovieManagementController extends Controller
{

    public function listAction(Request $request)
    {
        $manageForm = $this->createForm(MovieManagementType::class);
        $filterForm = $this->createForm(MovieManagementFilterType::class);
        $filterForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Cinemagia');
        $qb = $em->createQueryBuilder();

        $movieManageList = $repo->selectAllMovies($qb);

        if ($filterForm->isSubmitted()) {
            if ($filterForm->isValid()) {
                $movieFilters = $request->query->get('movie_management_filter');
                $mov = $repo->getMoviesByFilters($movieFilters);
                $pages = $movieFilters['results'];
            } else {
                $this->addFlash('error', 'Form-ul este invalid');
                $pages = 10;
                $mov = $repo->findAll();
            }
        } else {
            $pages = 10;
            $mov = $repo->findAll();
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $mov,
            $request->query->getInt('page',1),
            $pages
        );

        $qb = $em->createQueryBuilder();
//        $movies = $repo->selectAllMovies($qb);
        $movieManageList = $repo->findAll();

        if (empty($movieManageList)) {
            $this->addFlash('notice', $this->get('translator')->trans('group_management.list_empty'));
        }
        return $this->render(
            ':Admin:movie_manage_list.html.twig',
            [
                'movieManageList' => $movieManageList,
                'manageForm' => $manageForm->createView(),
                'filterForm' => $filterForm->createView(),
                'pagination' =>$pagination
            ]
        );

    }
    public function editAction(Request $request, Cinemagia $cinemagia)
    {
        $form = $this->createForm(MovieEditType::class, $cinemagia);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $groupRepo = $em->getRepository('AppBundle:Rottentomatoes');

        $movieManageList = $groupRepo->findAll();
        if (empty($movieManageList)) {
            $this->addFlash('notice', $this->get('translator')->trans('group_management.list_empty'));
        }

        return $this->render(':Admin:edit_movie.html.twig', [
            'form' => $form->createView(),
            'movieManageList' => $movieManageList,
        ]);
    }
}