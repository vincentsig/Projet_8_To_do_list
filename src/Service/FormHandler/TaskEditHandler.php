<?php

namespace App\Service\FormHandler;

use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TaskEditHandler extends AbstractHandler
{
    private EntityManagerInterface $em;
    protected const FORMTYPE = TaskType::class;
    private SessionInterface $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function process(object $data): void
    {
        $this->em->flush();

        $this->session->getFlashBag()->add('success', 'La tâche a bien été modifiée.');
    }
}
