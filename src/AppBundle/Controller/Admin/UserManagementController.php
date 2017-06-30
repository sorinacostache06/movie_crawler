<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use AppBundle\Form\UserManagementType;
use AppBundle\Form\UserFilterType;
use MongoDB\Driver\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserManagementController extends Controller
{
    /**
     * Action for listing the users.
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $manageForm = $this->createForm(UserManagementType::class);
        $filterForm = $this->createForm(UserFilterType::class);
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:User');

        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted()) {
            if ($filterForm->isValid()) {
                $userFilters = $request->query->get('user_filter');
                $qb = $repo->fetchAllFilteredQb($userFilters);
                $pages = $userFilters['results'];
            } else {
                $this->addFlash('error', 'Form-ul este invalid');
                $qb = $repo->selectAllQb();
                $pages = 10;
            }
        } else {
            $qb = $repo->selectAllQb();
            $pages = 10;
        }

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $qb,
                $request->query->getInt('page', 1),
                $pages
            );
            return $this->render(
                ':Admin:user_list.html.twig', [
                    'filterForm' => $filterForm->createView(),
                    'manageForm' => $manageForm->createView(),
                    'pagination' => $pagination,
                ]
            );
        }

    /**
     * Action that activates a user account.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function activateAction(Request $request, User $user)
    {
        $manageForm = $this->createForm(UserManagementType::class);
        $manageForm->handleRequest($request);
        if ($manageForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($user->isEnabled() == 1) {
                $this->addFlash('error', $this->get('translator')->trans('user_management.user_already_active'));
                return $this->redirectToRoute('user_management_list');
            }
            $user->setEnabled(1);
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('user_management.user_activated'));
        }
        return $this->redirectToRoute('user_management_list');
    }

    /**
     * Action that deactivates a user account.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function deactivateAction(Request $request, User $user)
    {
        $manageForm = $this->createForm(UserManagementType::class);
        $manageForm->handleRequest($request);
        if ($manageForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($user->isEnabled() == 0) {
                $this->addFlash('error', $this->get('translator')->trans('user_management.user_already_deactivated'));
                return $this->redirectToRoute('user_management_list');
            }
            $user->setEnabled(0);
            $em->flush();
            $this->addFlash('success', $this->get('translator')->trans('user_management.user_deactivated'));
        }
        return $this->redirectToRoute('user_management_list');
    }
}