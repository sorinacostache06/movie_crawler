<?php
/**
 * Created by Sorina Costache.
 * User: sorina
 * Date: 18.04.2017*/

namespace AppBundle\Repository;

/**
 * Class UserRepository
 * @package AppBundle\Repository
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function selectAllQb()
    {
        $qb = $this->createQueryBuilder('u');
        return $qb;
    }

    public function fetchAllFilteredQb($filterParams)
    {
        $qb = $this->selectAllQb();
        $qb = $this->addFilters($qb, $filterParams);
        return $qb;
    }

    public function addFilters($qb, $filterParams)
    {
        if (!(empty($filterParams['id']))) {
            $qb->andWhere('u.id LIKE :id')
                ->setParameter('id', '%'.$filterParams['id'].'%');
        }
        if (!(empty($filterParams['username']))) {
            $qb->andWhere('u.username LIKE :username')
                ->setParameter('username', '%'.$filterParams['username'].'%');
        }
        if (isset($filterParams['enabled'])&& $filterParams['enabled']!='All') {
            $qb->andWhere('u.enabled = :enabled')
                ->setParameter('enabled', $filterParams['enabled']);
        }
        if (!(empty($filterParams['joinDate']))) {
            $joinDate = new \DateTime($filterParams['joinDate']);
            $joinDate = $joinDate->format('Y-m-d H:i');
            $qb->andWhere('u.joinDate = :joinDate ')
                ->setParameter('joinDate', $filterParams['joinDate']);
        }
        if (!(empty($filterParams['email']))) {
        $qb->andWhere('u.email LIKE :email')
            ->setParameter('email', '%'.$filterParams['email'].'%');
         }
        return $qb;
    }

}