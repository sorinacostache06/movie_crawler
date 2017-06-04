<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Favorite;
use AppBundle\Entity\Movie;
use AppBundle\Entity\User;
use AppBundle\Form\AddToWatchType;
use AppBundle\Form\DeleteToWatchType;
use AppBundle\Form\WantToWatchListType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ListToWatchController extends Controller
{
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
                    $favorite->setWasWatched(true);
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

    public function listToWatchAction(Request $request)
    {
        $listForm = $this->createForm(WantToWatchListType::class);
        $manageForm = $this->createForm(DeleteToWatchType::class);
        $listForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Favorite');
        $qb = $em->createQueryBuilder();
        $favoritesManageList = [];
        if (count($this->getUser()) > 0){
            $favorites = $repo->selectAll($qb, $this->getUser()->getId());
            $favoritesManageList = $favorites->getQuery()->getResult();
        }
        if (empty($favoritesManageList)) {
            $this->addFlash('notice', $this->get('translator')->trans('favorites_list_empty'));
        }

        return $this->render(
            '::want_watch_list.html.twig', [
             'listMovies' => $listForm->createView(),
             'manageForm' => $manageForm->createView(),
             'favorites' => $favoritesManageList,
            ]
        );
    }

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
}