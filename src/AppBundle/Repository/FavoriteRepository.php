<?php

namespace AppBundle\Repository;

/**
 * FavoriteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FavoriteRepository extends \Doctrine\ORM\EntityRepository
{
    public function isDistincMovieInFavorites($qb,$title, $id)
    {
        $qb ->select('u')
            ->from('AppBundle:Favorite','u')
            ->andWhere('u.title = :title and u.user = :id')
            ->setParameter('title',$title)
            ->setParameter('id', $id);

        return $qb;
    }

    public function selectAll($qb)
    {
        $qb->select('u')
            ->from('AppBundle:Favorite','u')
            ->andWhere('u.title is not NULL');

        return $qb;
    }
}