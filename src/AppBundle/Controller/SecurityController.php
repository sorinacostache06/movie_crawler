<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class SecurityController extends Controller
{
    protected $user;

    /**
     * render form
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('home');
        }
//        // get the login error if there is one
//        $authenticationUtils = $this->get('security.authentication_utils');
//        $error = $authenticationUtils->getLastAuthenticationError();
        $this->user = new User();
        $form = $this->createForm(UserType::class, $this->user);
        $form->handleRequest($request);

        return $this->render(':Admin:login.html.twig', ['form' => $form->createView()]);
    }
}