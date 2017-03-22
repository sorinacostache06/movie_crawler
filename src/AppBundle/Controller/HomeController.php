<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function successAction(Request $request)
    {
        return $this->render(':Admin:success.html.twig',[]);
    }
}