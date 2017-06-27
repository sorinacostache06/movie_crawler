<?php
/**
 * Created by Sorina Costache.
 * User: sorina
 * Date: 18.04.2017*/

namespace AppBundle\Service;

use AppBundle\Entity\Site;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use AppBundle\Entity\Link;

class StartCrawlerService
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get all links from a site
     */
    public function startCrawlerAction($siteUrl)
    {
        $repoSite = $this->em->getRepository('AppBundle:Site');
        $site = $repoSite->findOneByName($siteUrl);
        $siteId = $site->getId();
        $originSite = $site->getName();

        $url = parse_url($siteUrl);
        $originHost = (isset($url['host'])) ? $url['host'] : '';


        $this->getAllDistinctLinks($siteUrl, $originSite, $originHost);
        $repo = $this->em->getRepository('AppBundle:Link');
        $qb = $this->em->createQueryBuilder();
        $access_repo = $repo->selectAll($qb,$siteId);
        $results = $access_repo->getQuery()->getResult();

        foreach ($results as $result) {
            $url = $result->getLink();
            $this->getAllDistinctLinks($url, $originSite, $originHost);
        }
    }

    /**
     * Insert all the distinct links in database
     * @param $siteUrl
     */
    public function getAllDistinctLinks($siteUrl, $originSite, $originHost)
    {

        $client = new Client();
        $crawler = new Crawler();
        $crawler = $client->request('GET', $siteUrl);

        $links = $crawler->filter('a')->links();

        foreach ($links as $link) {
            $l = $link->getUri();
            $url = parse_url($l);
            $host = (isset($url['host'])) ? $url['host'] : '';
            if (strcmp($host,$originHost) == 0) {
                $repo = $this->em->getRepository('AppBundle:Link');
                $qb = $this->em->createQueryBuilder();
                $access_repo = $repo->distinctLink($qb, $l);
                $result = $access_repo->getQuery()->getResult();
                $repoSite = $this->em->getRepository('AppBundle:Site');
                $site = $repoSite->findOneByName($originSite);
                if ($result == NULL) {
                    $link = new Link();
                    $link->setLink($l);
                    $link->setSite($site);
                    $site->addLink($link);
                    $this->em->persist($link);
                    $this->em->persist($site);
                    $this->em->flush();
                }
            }
        }
    }
}
