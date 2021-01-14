<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserHandler;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private $userHandler;

    public function __construct(UserHandler $userHandler)
    {
        $this->userHandler  =  $userHandler;
    }

    /**
     * @Route("/users", name="app_user_list")
     *
     * @param  UserRepository $repo
     * @return Response
     */
    public function listAction(UserRepository $repo): Response
    {
        return $this->render(
            'user/list.html.twig',
            ['users' => $repo->findAll()]
        );
    }

    /**
     * @Route("/users/create", name="app_user_create")
     *
     * @param  Request $request
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $user = new User();

        if ($this->userHandler->create($request, $user)) {
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $this->userHandler->getForm()->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="app_user_edit")
     *
     * @param  User $user
     * @param  Request $request
     * @return Response
     */
    public function editAction(User $user, Request $request): Response
    {
        if ($this->userHandler->edit($request, $user)) {
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $this->userHandler->getForm()->createView(), 'user' => $user]);
    }
}
