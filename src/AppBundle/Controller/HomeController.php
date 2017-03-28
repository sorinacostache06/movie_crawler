<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Goutte\Client;


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
        $client = new Client();
        $crawler = new Crawler();
        $crawler = $client->request('GET', 'http://www.cinemagia.ro/filme/moonlight-999089/');
        $titles = $crawler->filter('h1 > a');
        $year = $crawler->filter('a[class="link1"]')->text();
        echo $year;
//        foreach ($year as $y) {
//            echo $y->nodeValue."<br/>";
//        }
//        foreach ($titles as $title) {
//            echo $title->nodeValue."<br/>";
////            echo $link->nodeValue;
//        }
//        $links = $crawler->filter('a')->links();
//        foreach ($links as $link) {
//            echo $link->getUri()."<br/>";
////            echo $link->nodeValue;
//        }

        return new Response('Welcome!');
    }
}