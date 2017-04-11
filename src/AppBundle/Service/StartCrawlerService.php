<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use AppBundle\Entity\Test;

class StartCrawlerService
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function startCrawlerAction()
    {
        $this->getAllDistinctLinks('http://www.cinemagia.ro/');
        $repo = $this->em->getRepository('AppBundle:Test');
        $qb = $this->em->createQueryBuilder();
        $access_repo = $repo->selectAll($qb);
        $results = $access_repo->getQuery()->getResult();

        foreach ($results as $result) {
            $url = $result->getLink();
            $this->getAllDistinctLinks($url);
        }
    }

    public function getAllDistinctLinks($siteUrl)
    {

        $client = new Client();
        $crawler = new Crawler();
        $crawler = $client->request('GET', $siteUrl);

        $links = $crawler->filter('a')->links();
        foreach ($links as $link) {
            $l = $link->getUri();
            $url = parse_url($l);
            $host = (isset($url['host'])) ? $url['host'] : '';
            if (strcmp($host,"www.cinemagia.ro") == 0) {
                $repo = $this->em->getRepository('AppBundle:Test');
                $qb = $this->em->createQueryBuilder();
                $access_repo = $repo->distinctLink($qb, $l);
                $result = $access_repo->getQuery()->getResult();
                if ($result == NULL) {
                    $test = new Test();
                    $test->setLink($l);
                    $this->em->persist($test);
                    $this->em->flush();
                }
            }
        }
    }
}