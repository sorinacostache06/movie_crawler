<?php
/**
 * Created by Sorina Costache.
 * User: sorina
 * Date: 18.04.2017*/

namespace AppBundle\Controller;

use AppBundle\Entity\Favorite;
use AppBundle\Entity\User;
use AppBundle\Form\LanguageType;
use AppBundle\Form\AccountType;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    protected $user;

    /**
     * Login function
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('user_management_list');
        }
        $languageForm = $this->createForm(LanguageType::class);


        $this->user = new User();
        $form = $this->createForm(UserType::class, $this->user);
        $form->handleRequest($request);

        return $this->render(':Admin:login.html.twig', ['form' => $form->createView(),
            'languageForm' => $languageForm->createView(),
        ]);
    }

    /**
     * Create new account
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAccountAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (strcmp($form->get('password')->getData(),$form->get('password_again')->getData()) == 0) {
                $favorite = new Favorite();
                $date = new \DateTime("now", new DateTimeZone('UTC'));
                $user->setJoinDate($date);
                $user->setEnabled(true);
                $user->addFavorite($favorite);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->persist($favorite);
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
