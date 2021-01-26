<?php

namespace App\Service\FormHandler;

use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TaskCreateHandler extends AbstractHandler
{
    private EntityManagerInterface $em;
    protected const FORMTYPE = TaskType::class;
    private TokenStorageInterface $tokenStorage;
    private SessionInterface $session;

    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    public function process(object $data): void
    {
        $data->setAuthor($this->tokenStorage->getToken()->getUser());
        $this->em->persist($data);
        $this->em->flush();

        $this->session->getFlashBag()->add('success', 'La tâche a bien été ajoutée.');
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }
}
