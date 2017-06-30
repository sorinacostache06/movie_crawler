<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Favorite;
use AppBundle\Entity\Movie;
use AppBundle\Entity\User;
use AppBundle\Form\AddToWatchType;
use AppBundle\Form\DeleteToWatchType;
use AppBundle\Form\WantToWatchFilterType;
use AppBundle\Form\WantToWatchListType;
use AppBundle\Form\WatchListFilterType;
use AppBundle\Form\WatchlistManagementType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ListToWatchController extends Controller
{
    /**
     * @param Request $request
     * @param Movie $movie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToWatchAction(Request $request, Movie $movie)
    {
        $manageForm = $this->createForm(AddToWatchType::class);
        $manageForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Favorite');

        if ($manageForm->isValid()) {
            $user = $this->getUser();
            if ($user == NULL) {
                $this->addFlash('error', $this->get('translator')->trans('user.not.logged'));
            } else {
                $favorite = new Favorite();
                $qb = $em->createQueryBuilder();
                $result = $repo->isDistincMovieInFavorites($qb,$movie->getTitle(),$user->getId());
                $isDistinct = $result->getQuery()->getResult();
                if (count($isDistinct) == 0) {
                    $favorite->setTitle($movie->getTitle());
                    $favorite->setRating($movie->getRating());
                    $favorite->setWasWatched(false);
                    $favorite->setUser($user);
                    $user->addFavorite($favorite);
                    $em->persist($favorite);
                    $em->persist($user);
                    $em->flush();
                }
                else {
                    $this->addFlash('notice', $this->get('translator')->trans('already.in.favorites'));
                }
            }
        }
        return $this->redirectToRoute('home');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listToWatchAction(Request $request)
    {
        $listForm = $this->createForm(WantToWatchListType::class);
        $manageForm = $this->createForm(DeleteToWatchType::class);
        $filterForm = $this->createForm(WatchListFilterType::class);
        $manageForm2 = $this->createForm(WatchlistManagementType::class);
        $filterForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Favorite');
        $qb = $em->createQueryBuilder();
        $favoritesManageList = [];

        if (count($this->getUser()) > 0) {
            if ($filterForm->isSubmitted()) {
                if ($filterForm->isValid()) {
                    $watchlistFilters = $request->query->get('watch_list_filter');
                    $qb = $repo->fetchAllFilteredQb($watchlistFilters, $this->getUser()->getId());
                    $pages = $watchlistFilters['results'];
                } else {
                    $this->addFlash('error', 'Form-ul este invalid');
                    $qb = $repo->selectAll($qb, $this->getUser()->getId());
                    $pages = 10;
                }
            } else {
                    $favorites = $repo->selectAll($qb, $this->getUser()->getId());
                    $favoritesManageList = $favorites->getQuery()->getResult();
                if (empty($favoritesManageList)) {
                    $this->addFlash('notice', $this->get('translator')->trans('favorites_list_empty'));
                }
                $pages = 10;
            }
        } else {
            return $this->redirectToRoute('login');
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $pages
        );

        return $this->render(
            '::want_watch_list.html.twig', [
             'listMovies' => $listForm->createView(),
             'manageForm' => $manageForm->createView(),
                'manageForm2' => $manageForm2->createView(),
                'filterForm' => $filterForm->createView(),
             'favorites' => $favoritesManageList,
             'pagination' => $pagination
            ]
        );
    }

    /**
     * @param Request $request
     * @param Favorite $favorite
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteToWatchAction(Request $request, Favorite $favorite)
    {
        $manageForm = $this->createForm(DeleteToWatchType::class);
        $manageForm->handleRequest($request);

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('AppBundle:Favorite');
        $qb = $em->createQueryBuilder();
        if ($manageForm->isValid()){
            $qbResults = $repo->deleteFromFavorites($qb, $favorite->getTitle());
            $results = $qbResults->getQuery()->getResult();
            foreach ($results as $result){
                $em->remove($result);
            }
            $em->flush();
        }

        return $this->redirectToRoute('want_to_watch');

    }

    /**
     * Action that activates a user account.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("favorite", class="AppBundle:Favorite")
     */
    public function activateAction(Request $request, Favorite $favorite)
    {
        $manageForm = $this->createForm(WatchlistManagementType::class);
        $manageForm->handleRequest($request);
        if ($manageForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($favorite->getWasWatched() == 1) {
                $this->addFlash('error', $this->get('translator')->trans('list_management.user_already_active'));
                return $this->redirectToRoute('want_to_watch');
            }
            $favorite->setWasWatched(true);
            $em->flush($favorite);
            $this->addFlash('success', $this->get('translator')->trans('list_management.user_activated'));
        }
        return $this->redirectToRoute('want_to_watch');
    }

    /**
     * Action that deactivates a user account.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("favorite", class="AppBundle:Favorite")
     */
    public function deactivateAction(Request $request, Favorite $favorite)
    {
        $manageForm = $this->createForm(WatchlistManagementType::class);
        $manageForm->handleRequest($request);
        if ($manageForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($favorite->getWasWatched() == 0) {
                $this->addFlash('error', $this->get('translator')->trans('list_management.user_already_deactivated'));
                return $this->redirectToRoute('want_to_watch');
            }
            $favorite->setWasWatched(false);
            $em->flush($favorite);
            $this->addFlash('success', $this->get('translator')->trans('list_management.user_deactivated'));
        }
        return $this->redirectToRoute('want_to_watch');
    }
}