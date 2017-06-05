<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use DateTimeZone;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime("now", new DateTimeZone('UTC'));

        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setPassword('admin');
        $userAdmin->setJoinDate($date);
        $userAdmin->setEmail("admin@admin.com");
        $userAdmin->setEnabled(1);
        $userAdmin->setRoles(['ROLE_ADMIN']);

        $manager->persist($userAdmin);
        $manager->flush();
    }
}