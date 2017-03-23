<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\AccountType;
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

    public function createAccountAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (strcmp($form->get('password')->getData(),$form->get('password_again')->getData()) == 0) {
                $date = new \DateTime("now", new DateTimeZone('UTC'));
                $user->setJoinDate($date);
                $user->setEnabled(true);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return $this->redirectToRoute('home');
            }
            else {
                $this->addFlash('error','Form-ul este invalid');
            }
        }

        return $this->render(':Admin:create_account.html.twig', ['form' => $form->createView()]);
    }
}