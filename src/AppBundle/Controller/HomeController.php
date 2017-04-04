<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Goutte\Client;
use AppBundle\Entity\Test;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class HomeController extends Controller
{
    public function successAction(Request $request)
    {
        return $this->render(':Admin:success.html.twig',[]);
    }

    public function crowAction(Request $request)
    {
//        $html = <<<'HTML'
//<!DOCTYPE html>
//<html>
//    <body>
//        <p class="message">Hello World!</p>
//        <p>Hello Crawler!</p>
//    </body>
//</html>
//HTML;
//
//        $crawler = new Crawler($html);
//
//        foreach ($crawler as $domElement) {
//            var_dump($domElement->nodeValue);
//        }
//        $client = new Client();
//        $crawler = new Crawler();
//        $crawler = $client->request('GET', 'http://www.cinemagia.ro/');
//
//        $links = $crawler->filter('a')->links();
////        foreach ($links as $link) {
////           echo $l = $link->getUri()."<br/>";
////        }
//        foreach ($links as $link) {
//            $l = $link->getUri()."<br/>";
//            $url = parse_url($l);
//            $host = (isset($url['host'])) ? $url['host'] : '';
//            if (strcmp($host,"www.cinemagia.ro") == 0) {
//                $path = explode('/',$url["path"]);
//                if (count($path) == 5 and strcmp($path[1],"filme") == 0 and strlen($path[3]) == 3 ) {
//                    $name = explode('-',$path[2]);
//                    if (is_numeric($name[count($name) -1])){
//                        echo $l;
////                        $titles_crowler = $crawler->filter('h1 > a');
////                        $year = $crawler->filter('a[class="link1"]')->text();
////                        foreach ($titles_crowler as $t) {
////                            $title = $t->nodeValue;
////                        }
////
////                        echo $l . " " . $year . " " . $title . "<br/>";
//                    }
//                }
//            }


//            $vec = explode("/",$l);
////            var_dump($vec);
////            for ($i=0; $i<count($vec); $i++) {
////                echo $i . $vec[$i] . "<br/>";
////            }
//            if (count($vec) == 7 and strcmp($vec[2],"www.cinemagia.ro") == 0 and strcmp($vec[3],"filme") == 0 and strcmp($vec[5],"#>") == 0) {
//                print_r ($l);
//            }
////            echo $link->nodeValue;
//        }
        $this->recAction('http://www.cinemagia.ro/');

        return new Response('Welcome!');
    }

    public function recAction($request_url)
    {
        $client = new Client();
        $crawler = new Crawler();
        $crawler = $client->request('GET', $request_url);

        $links = $crawler->filter('a')->links();
        foreach ($links as $link) {
            $l = $link->getUri();
            $url = parse_url($l);
            $host = (isset($url['host'])) ? $url['host'] : '';
            $path_url = (isset($url['path'])) ? $url['path'] : '';
            if (strcmp($host,"www.cinemagia.ro") == 0) {
                $path = explode('/',$path_url);
                if (count($path) == 4 and strcmp($path[1],"filme") == 0 and strcmp($path[3],"") == 0) {
                    $name = explode('-',$path[2]);
                    if (is_numeric($name[count($name) -1])){
                        echo $l . "<br/>";
                        $em = $this->getDoctrine()->getManager();
                        $repo = $em->getRepository('AppBundle:Test');
                        $qb = $em->createQueryBuilder();
                        $result = $repo->distinctLink($qb,$l);
                        print_r($result);
                        die();
                        try {
                            $test = new Test();
                            $test->setLink($l);
                            $em->persist($test);
                            $em->flush();
                        } catch(ORMException $e){
                            echo "Sorry, but someone else has already changed this entity. Please apply the changes again!";
                        }
                        $this->recAction($l);
                    }
                }
            }
        }
    }
}