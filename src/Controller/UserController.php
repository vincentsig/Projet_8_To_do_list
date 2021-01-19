<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FormHandler\UserCreateHandler;
use App\Service\FormHandler\UserEditHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="app_user_list")
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
     * @param Request $request
     * @param UserCreateHandler $hanlder
     * @return Response
     */
    public function createAction(Request $request, UserCreateHandler $handler): Response
    {
        $user = new User();

        if ($handler->handle($request, $user, ['validation_groups' => ['Default', 'user_create']])) {
            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $handler->getForm()->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="app_user_edit")
     *
     * @param  User $user
     * @param  Request $request
     * @param  UserEditHandler $handler
     * @return Response
     */
    public function editAction(User $user, Request $request, UserEditHandler $handler): Response
    {
        if ($handler->handle($request, $user)) {
            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/edit.html.twig', [
        'form' => $handler->getForm()->createView(), 'user' => $user
            ]);
    }
}
